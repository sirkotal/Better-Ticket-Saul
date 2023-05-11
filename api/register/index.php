<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/session.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::POST:
      $session = new Session();
      if ($session->isLoggedIn()) {
        API::sendError(HTTPStatus::FORBIDDEN, 'Already logged in.');
        return;
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HTTPStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('username', $data) || !array_key_exists('name', $data) || !array_key_exists('email', $data) || !array_key_exists('password', $data)) {
        API::sendError(HTTPStatus::BAD_REQUEST, 'Missing required fields');
        return;
      }

      if (gettype($data['username']) != 'string' || gettype($data['name']) != 'string' || gettype($data['email']) != 'string' || gettype($data['password']) != 'string') {
        API::sendError(HTTPStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($data) != 4) {
        API::sendError(HTTPStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      if (User::exists($data['username'])) {
        API::sendError(HTTPStatus::FORBIDDEN, 'User already exists');
        return;
      }

      if (User::emailExists($data['email'])) {
        API::sendError(HTTPStatus::FORBIDDEN, 'Email already in use');
        return;
      }

      $user = User::create($data['username'], $data['name'], $data['email'], $data['password']);
      $session->setUser($data['username']);

      API::sendResponse(HTTPStatus::CREATED, ['message' => 'User created successfully', 'body' => $user->parseJsonInfo()]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      break;
  }
?>
