<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../lib/session.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::POST:
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

      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You must be logged in to do that');
        die();
      }

      if (!User::isAdmin($session->getUser()->getId())) {
        API::sendError(HttpStatus::FORBIDDEN, 'You must be an admin to do that');
        die();
      }

      try {
        $user = User::getUserById((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        die();
      }

      if (User::isAdmin($user->getId())) {
        API::sendError(HttpStatus::BAD_REQUEST, 'User is already an admin');
        die();
      }

      $user = User::makeAdmin($user->getId());

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User is now an admin',
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

      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You must be logged in to do that');
        die();
      }

      if (!User::isAdmin($session->getUser()->getId())) {
        API::sendError(HttpStatus::FORBIDDEN, 'You must be an admin to do that');
        die();
      }

      try {
        $user = User::getUserById((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'User not found');
        die();
      }

      if (!User::isAdmin($user->getId())) {
        API::sendError(HttpStatus::BAD_REQUEST, 'User is not an admin');
        die();
      }

      $user = User::demoteAdmin($user->getId());

      API::sendResponse(HttpStatus::OK, [
        'message' => 'User is no longer an admin',
        'body' => $user->parseJsonInfo()
      ]);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::POST, RequestMethod::DELETE, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      die();
  }
?>
