<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../lib/http_status.php');
  require_once(__DIR__ . '/../lib/api.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      $body = [
        'health' => 'ok'
      ];

      API::sendResponse(HttpStatus::OK, $body);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::GET, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
