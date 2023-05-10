<?php
  declare(strict_types=1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../lib/session.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::POST:
      $json_data = file_get_contents('php://input');
      $data = json_decode($json_data, true);

      if (!empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::FORBIDDEN, 'You are not logged in');
        return;
      }

      $session->logout();
      API::sendPostResponse(HttpStatus::OK, ['message' => 'Logged out.']);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
