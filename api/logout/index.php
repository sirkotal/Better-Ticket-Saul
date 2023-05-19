<?php
  declare(strict_types=1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../lib/session.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::POST:
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::FORBIDDEN, 'You are not logged in');
        die();
      }

      $session->logout();
      API::sendResponse(HttpStatus::OK, ['message' => 'Logged out.']);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::POST, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
