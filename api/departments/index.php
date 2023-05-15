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
        die();
      }

      // get department by id
      if (isset($parts[3])) {
        if (!is_numeric($parts[3])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          die();
        }

        $department = new Department((int) $parts[3]);

        API::sendResponse(HttpStatus::OK, Department::parseJsonInfo($department));
        die();
      }

      $departments = Department::getAllDepartments();
      $body = [];

      foreach ($departments as $department) {
        $body[] = Department::parseJsonInfo($department);
      }

      API::sendResponse(HttpStatus::OK, $body);
      die();
    case RequestMethod::POST:
      $data = API::getJsonInput();

      if (!array_key_exists('name', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        die();
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      $name = $data['name'];

      if (Department::exists($name)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department already exists');
        die();
      }

      $department = Department::create($name);
      $body = Department::parseJsonInfo($department);

      API::sendResponse(HttpStatus::CREATED, [
        'message' => 'Department created successfully',
        'body' => $body
      ]);
      die();
    case RequestMethod::PUT:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (isset($parts[3]) && !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      try {
        $department = new Department((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Department not found');
        die();
      }
      
      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        die();
      }

      if (!array_key_exists('name', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        die();
      }

      if (gettype($data['name']) != 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      try {
        $department->update($data['name']);
      } catch (Exception $e) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department already exists');
        die();
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Department updated successfully',
        'body' => Department::parseJsonInfo($department)
      ]);
      die();
    case RequestMethod::DELETE:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      if (!is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      try {
        $body = Department::delete((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Department not found');
        die();
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Department deleted successfully',
        'body' => $body
      ]);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::GET, RequestMethod::POST, RequestMethod::PUT, RequestMethod::DELETE, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      die();
  }
?>
