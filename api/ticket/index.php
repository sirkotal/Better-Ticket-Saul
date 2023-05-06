<?php
  declare(strict_types=1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/ticket.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      // get ticket by id
      if ($parts[3]) {
        if (!is_numeric($parts[3])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        try {
          $ticket = new Ticket((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
          return;
        }

        $body = [
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

        API::sendGetResponse(HttpStatus::OK, $body);
        return;
      }

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
    case RequestMethod::POST:
      API::getSessionAuth(); // don't need the session, just need to check if logged in

      $json_data = file_get_contents('php://input');
      $data = json_decode($json_data, true);

      if (!array_key_exists('title', $data) || !array_key_exists('text', $data) || !array_key_exists('client', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        return;
      }

      if (gettype($data['title']) != 'string' || gettype($data['text']) != 'string' || gettype($data['client']) != 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (array_key_exists('hashtags', $data) && !is_array($data['hashtags'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (array_key_exists('department', $data) && gettype($data['department']) != 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      $just_hashtags = array_key_exists('hashtags', $data) && count($data) > 4;
      $just_department = array_key_exists('department', $data) && count($data) > 4;
      $both = array_key_exists('hashtags', $data) && array_key_exists('department', $data) && count($data) > 5;
      if (count($data) > 3 || $just_hashtags || $just_department || $both) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      if (!Client::exists($data['client'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Client does not exist');
        return;
      }

      if (isset($data['department']) && !Department::exists($data['department'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
        return;
      }

      $hashtags = isset($data['hashtags']) ? $data['hashtags'] : [];
      $department = isset($data['department']) ? $data['department'] : null;

      Ticket::create($data['title'], $data['text'], new Client($data['client']), $hashtags, $department);

      API::sendPostResponse(HttpStatus::CREATED, ['message' => 'Ticket created']);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
