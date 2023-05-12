<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::POST:
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
        $user = User::getUserById((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        return;
      }

      if (User::isAgent($user->getId())) {
        API::sendError(HttpStatus::BAD_REQUEST, 'User is already an agent');
        return;
      }

      $user = User::makeAgent($user->getId());

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User is now an agent',
        'body' => $user->parseJsonInfo()
      ]);
      return;
    case RequestMethod::PUT:
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
        $user = User::getUserById((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        return;
      }

      if (!User::isAgent($user->getId())) {
        API::sendError(HttpStatus::BAD_REQUEST, 'User is not an agent');
        return;
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'JSON body is empty');
        return;
      }

      if (!isset($data['action']) || !isset($data['departmentId'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing fields');
        return;
      }

      if (!is_string($data['action']) || !is_numeric($data['departmentId'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (array_diff_key($data, array_flip(['action', 'departmentId']))) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      if ($data['action'] != 'add' && $data['action'] != 'remove') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid action');
        return;
      }

      try {
        $department = new Department((int) $data['departmentId']);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Department not found');
        return;
      }

      if ($data['action'] == 'add') {
        $department->addAgent(new Agent($user->getId()));

        API::sendResponse(HttpStatus::OK, [
          'message' => 'Agent added to department',
          'body' => Department::parseJsonInfo($department)
        ]);
      } else {
        $department->removeAgent(new Agent($user->getId()));

        API::sendResponse(HttpStatus::OK, [
          'message' => 'Agent removed from department',
          'body' => Department::parseJsonInfo($department)
        ]);
      }

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
        $user = User::getUserById((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        return;
      }

      if (!User::isAgent($user->getId())) {
        API::sendError(HttpStatus::BAD_REQUEST, 'User is not an agent');
        return;
      }

      $user = User::demoteAgent($user->getId());

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User is no longer an agent',
        'body' => $user->parseJsonInfo()
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
