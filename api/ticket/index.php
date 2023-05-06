<?php
  declare(strict_types=1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/ticket.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $tickets = Ticket::getAllTickets();

      $body = [];

      foreach ($tickets as $ticket) {
        $body[] = [
          'id' => $ticket->getId(),
          'title' => $ticket->getTitle(),
          'text' => $ticket->getText(),
          'date' => $ticket->getDate(),
          'status' => $ticket->getStatus(),
          'priority' => $ticket->getPriority(),
          'client' => $ticket->getClient()->getName(),
          'agent' => $ticket->getAgent() ? $ticket->getAgent()->getName() : null,
          'department' => $ticket->getDepartment() ? $ticket->getDepartment()->getName() : null,
          'hashtags' => $ticket->getHashtags()
        ];
      }

      API::sendGetResponse(HttpStatus::OK, $body);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
