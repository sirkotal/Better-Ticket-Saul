<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      // get user by id
      if (isset($parts[3])) {
        $id = $parts[3];

        if (!is_numeric($id)) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid id');
          return;
        }

        if (User::exists((int) $id) === false) {
          API::sendError(HttpStatus::NOT_FOUND, 'User not found');
          return;
        }

        $user = User::getUserById((int) $id);

        API::sendResponse(HttpStatus::OK, $user->parseJsonInfo());
        return;
      }

      $users = User::getAllUsers();
      $body = [];

      foreach ($users as $user) {
        $body[] = $user->parseJsonInfo();
      }
      
      API::sendResponse(HttpStatus::OK, $body);
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
        $body = User::delete((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        return;
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User deleted successfully',
        'body' => $body
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
