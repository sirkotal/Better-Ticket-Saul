<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../lib/session.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::POST:
      $session = new Session();
      if ($session->isLoggedIn()) {
        API::sendError(HttpStatus::FORBIDDEN, 'You are already logged in');
        return;
      }

      $data = API::getJsonInput();

      if (!array_key_exists('username', $data) || !array_key_exists('password', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        return;
      }

      if (gettype($data['username']) != 'string' || gettype($data['password']) != 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      if (User::isValid($data['username'], $data['password'])) {
        $session->setUser($data['username']);
        API::sendPostResponse(HttpStatus::OK, ['message' => 'Logged in as ' . $data['username'] . '.']);
      } else {
        API::sendError(HttpStatus::UNAUTHORIZED, 'Invalid username or password');
      }
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
