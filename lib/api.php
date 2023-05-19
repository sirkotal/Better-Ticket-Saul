<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/http_status.php');
  require_once(__DIR__ . '/session.php');

  abstract class RequestMethod {
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';
  }

  class API {
    static function sendResponse(int $status, array $body) {
      http_response_code($status);
      header('Content-Type: application/json');
      echo json_encode($body);
    }

    static function sendError(int $status, string $message) {
      http_response_code($status);
      header('Content-Type: application/json');
      echo json_encode(['error' => $message]);
    }

    static function getJsonInput(): array {
      $input = file_get_contents('php://input');
      $json = json_decode($input, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid JSON');
        die();
      }

      return $json;
    }

    static function corsSetup(string ...$methods) {
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: ' . implode(', ', $methods));
      header('Access-Control-Allow-Headers: Content-Type');
    }
  }
?>
