<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/ticket.php');
  require_once(__DIR__ . '/../../database/ticket_log.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      if (isset($parts[3]) && !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      // get replies by id
      if (isset($parts[3])) {
        try {
          $log = new TicketLog((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Ticket log not found');
          die();
        }

        API::sendResponse(HttpStatus::OK, $log->parseJsonInfo());
        die();
      }

      $body = [];
      foreach (TicketLog::getAllLogs() as $log) {
        $body[] = $log->parseJsonInfo();
      }

      API::sendResponse(HttpStatus::OK, $body);
      die();
    case RequestMethod::POST:
      //? maybe add a BOT
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You must be logged in to do that');
        die();
      }

      if (!User::isAdmin($session->getUser()->getId())) {
        API::sendError(HttpStatus::FORBIDDEN, 'You must be an admin to do that');
        die();
      }
      
      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        die();
      }

      if (!array_key_exists('change', $data) || !array_key_exists('ticketId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (gettype($data['change']) !== 'string' || gettype($data['ticketId']) !== 'integer') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      try {
        $ticket = new Ticket($data['ticketId']);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
        die();
      }

      $log = TicketLog::create($data['change'], $ticket->getId(), $ticket->getAgent()->getId(), $ticket->getDepartment()->getId());

      API::sendResponse(HttpStatus::CREATED, [
        'message' => 'Ticket log created successfully',
        'body' => $log->parseJsonInfo()
      ]);
      die();
    case RequestMethod::PUT:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
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
        $log = new TicketLog((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket log not found');
        die();
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        die();
      }

      if (!array_key_exists('change', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        die();
      }

      if (gettype($data['change']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      $log->update($data['change']);

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket log updated successfully',
        'body' => $log->parseJsonInfo()
      ]);
      die();
    case RequestMethod::DELETE:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
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

      $body = TicketLog::delete((int) $parts[3]);
      
      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket log deleted successfully',
        'body' => $body
      ]);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::GET, RequestMethod::POST, RequestMethod::PUT, RequestMethod::DELETE, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      die();
  }
?>
