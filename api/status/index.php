<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/ticket.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $queries_str = $url['query'];
      
      if ($queries_str === null) {
        $status = TicketStatus::getAll();
        
        API::sendResponse(HttpStatus::OK, $status);
        die();
      }
      
      parse_str($queries_str, $queries);
      try {
        $status = TicketStatus::getColor($queries['status']);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket status not found');
        die();
      }

      API::sendResponse(HttpStatus::OK, $status);
      die();
    case RequestMethod::POST:
      $data = API::getJsonInput();
      echo 'php';
      if (!array_key_exists('status', $data)) {
          API::sendResponse(HttpStatus::BAD_REQUEST, 'Missing required field');
          die();
      }

      if (!array_key_exists('color', $data)) {
        API::sendResponse(HttpStatus::BAD_REQUEST, 'Missing required field');
        die();
    }
      
      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }
      
      TicketStatus::create($data['status'],$data['color']);
      API::sendResponse(HttpStatus::CREATED, [
          'message' => 'Status created successfully',
          'body' => [
            'status' => $data['status'],
            'color' => $data['color']
          ]          
        ]);
      die();
    case RequestMethod::OPTIONS:
      // add unique CORS because this is for private use
      header('Access-Control-Allow-Origin: localhost:9000'); //! this is the url of our website
      header('Access-Control-Allow-Methods: GET, OPTIONS');
      header('Access-Control-Allow-Headers: Content-Type');
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      die();
  }
?>
