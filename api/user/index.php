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
      if ($parts[3]) {
        $id = $parts[3];

        if (!is_numeric($id)) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid id');
          return;
        }

        try {
          $user = User::getUserById((int) $id);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'User not found');
          return;
        }

        $isAgent = false;
        if (User::isAdmin($user->getUsername())) {
          $isAgent = true;
          $role = 'admin';
        } else if (User::isAgent($user->getUsername())) {
          $isAgent = true;
          $role = 'agent';
        } else {
          $role = 'client';
        }

        $body = [
          'id' => $user->getId(),
          'username' => $user->getUsername(),
          'name' => $user->getName(),
          'email' => $user->getEmail(),
          'role' => $role
        ];

        if ($isAgent) {
          $body['departments'] = $user->getDepartments();
        }

        API::sendGetResponse(HttpStatus::OK, $body);
        return;
      }

      $users = User::getAllUsers();
      $body = [];

      foreach ($users as $user) {
        $body[] = [
          'id' => $user->getId(),
          'username' => $user->getUsername(),
          'name' => $user->getName(),
          'email' => $user->getEmail(),
        ];

        if (User::isAdmin($user->getUsername())) {
          $body[count($body) - 1]['role'] = 'admin';
        } else if (User::isAgent($user->getUsername())) {
          $body[count($body) - 1]['role'] = 'agent';
        } else {
          $body[count($body) - 1]['role'] = 'client';
        }

        if (User::isAgent($user->getUsername())) {
          $body[count($body) - 1]['departments'] = $user->getDepartments();
        }
      }
      
      API::sendGetResponse(HttpStatus::OK, $body);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
