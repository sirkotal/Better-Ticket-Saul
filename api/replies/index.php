<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/ticket.php');
  require_once(__DIR__ . '/../../database/ticket_reply.php');

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
          $reply = new TicketReply((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Ticket reply not found');
          return;
        }

        API::sendResponse(HttpStatus::OK, $reply->parseJsonInfo());
        return;
      }

      $body = [];
      foreach (TicketReply::getAllReplies() as $reply) {
        $body[] = $reply->parseJsonInfo();
      }

      API::sendResponse(HttpStatus::OK, $body);
      return;
    case RequestMethod::POST:
      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('reply', $data) || !array_key_exists('ticketId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (gettype($data['reply']) !== 'string' || gettype($data['ticketId']) !== 'integer') {
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

      $reply = TicketReply::create($data['reply'], $ticket->getId(), $ticket->getAgent()->getId(), $ticket->getDepartment()->getId());

      API::sendResponse(HttpStatus::CREATED, [
        'message' => 'Ticket reply created successfully',
        'body' => $reply->parseJsonInfo()
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
        $reply = new TicketReply((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket reply not found');
        return;
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('reply', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        return;
      }

      if (gettype($data['reply']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      $reply->update($data['reply']);

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket reply updated successfully',
        'body' => $reply->parseJsonInfo()
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

      $body = TicketReply::delete((int) $parts[3]);
      
      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket reply deleted successfully',
        'body' => $body
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
