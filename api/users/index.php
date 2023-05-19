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
        die();
      }

      // get user by id
      if (isset($parts[3])) {
        $id = $parts[3];

        if (!is_numeric($id)) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid id');
          die();
        }

        if (User::exists((int) $id) === false) {
          API::sendError(HttpStatus::NOT_FOUND, 'User not found');
          die();
        }

        $user = User::getUserById((int) $id);

        API::sendResponse(HttpStatus::OK, $user->parseJsonInfo());
        die();
      }

      $users = User::getAllUsers();
      $body = [];

      foreach ($users as $user) {
        $body[] = $user->parseJsonInfo();
      }
      
      API::sendResponse(HttpStatus::OK, $body);
      die();
    case RequestMethod::PUT:
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not logged in');
        die();
      }
      
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (!User::exists((int) $parts[3])) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        die();
      }

      $user = User::getUserById((int) $parts[3]);
      if ($user->getId() !== $session->getUser()->getId()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not authorized to edit this user');
        die();
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'JSON body is empty');
        die();
      }

      if (!isset($data['username']) && !isset($data['name']) && !isset($data['email']) && !isset($data['password'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (array_diff_key($data, array_flip(['username', 'name', 'email', 'password']))) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      if (isset($data['username']) && !is_string($data['username'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (isset($data['name']) && !is_string($data['name'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (isset($data['email']) && !is_string($data['email'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (isset($data['password']) && !is_string($data['password'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (isset($data['username'])) {
        //! just a basic check for now
        if (strlen($data['username']) < 3) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Username must be at least 3 characters long');
          die();
        }
        
        if (User::exists($data['username'])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Username already exists');
          die();
        }

        $user->setUsername($data['username']);
      }

      if (isset($data['name'])) {
        //! just a basic check for now
        if (strlen($data['name']) < 3) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Name must be at least 3 characters long');
          die();
        }

        $user->setName($data['name']);
      }

      if (isset($data['email'])) {
        if (User::emailExists($data['email'])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Email already exists');
          die();
        }

        $user->setEmail($data['email']);
      }

      if (isset($data['password'])) {
        //! just a basic check for now
        if (strlen($data['password']) < 3) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Password must be at least 3 characters long');
          die();
        }

        $user->setPassword($data['password']);
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User updated successfully',
        'body' => $user->parseJsonInfo()
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

      $user = User::getUserById((int) $parts[3]);
      if ($user->getId() !== $session->getUser()->getId() || !User::isAdmin($user->getId())) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not authorized to delete this user');
        die();
      }

      try {
        $body = User::delete((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        die();
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User deleted successfully',
        'body' => $body
      ]);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::GET, RequestMethod::PUT, RequestMethod::DELETE, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      die();
  }
?>