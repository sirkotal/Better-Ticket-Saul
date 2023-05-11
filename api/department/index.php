<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/department.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      // get department by id
      if (isset($parts[3])) {
        if (!is_numeric($parts[3])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        $department = new Department((int) $parts[3]);

        API::sendResponse(HttpStatus::OK, Department::parseJsonInfo($department));
        return;
      }

      $departments = Department::getAllDepartments();
      $body = [];

      foreach ($departments as $department) {
        $body[] = Department::parseJsonInfo($department);
      }

      API::sendResponse(HttpStatus::OK, $body);
      return;
    case RequestMethod::POST:
      $data = API::getJsonInput();

      if (!array_key_exists('name', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        return;
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      $name = $data['name'];

      if (Department::exists($name)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department already exists');
        return;
      }

      $department = Department::create($name);
      $body = Department::parseJsonInfo($department);

      API::sendResponse(HttpStatus::CREATED, [
        'message' => 'Department created successfully',
        'body' => $body
      ]);
      return;
    case RequestMethod::DELETE:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      if (!is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      try {
        $body = Department::delete((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Department not found');
        return;
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Department deleted successfully',
        'body' => $body
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
