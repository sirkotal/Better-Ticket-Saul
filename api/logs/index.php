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
        return;
      }

      if (isset($parts[3]) && !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      // get replies by id
      if (isset($parts[3])) {
        try {
          $log = new TicketLog((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Ticket log not found');
          return;
        }

        API::sendResponse(HttpStatus::OK, $log->parseJsonInfo());
        return;
      }

      $body = [];
      foreach (TicketLog::getAllLogs() as $log) {
        $body[] = $log->parseJsonInfo();
      }

      API::sendResponse(HttpStatus::OK, $body);
      return;
    case RequestMethod::POST:
      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('change', $data) || !array_key_exists('ticketId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (gettype($data['change']) !== 'string' || gettype($data['ticketId']) !== 'integer') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      try {
        $ticket = new Ticket($data['ticketId']);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
        return;
      }

      $log = TicketLog::create($data['change'], $ticket->getId(), $ticket->getAgent()->getId(), $ticket->getDepartment()->getId());

      API::sendResponse(HttpStatus::CREATED, [
        'message' => 'Ticket log created successfully',
        'body' => $log->parseJsonInfo()
      ]);
      return;
    case RequestMethod::PUT:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      try {
        $log = new TicketLog((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket log not found');
        return;
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('change', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        return;
      }

      if (gettype($data['change']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      $log->update($data['change']);

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket log updated successfully',
        'body' => $log->parseJsonInfo()
      ]);
      return;
    case RequestMethod::DELETE:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      $body = TicketLog::delete((int) $parts[3]);
      
      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket log deleted successfully',
        'body' => $body
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
