<?php
  declare(strict_types=1);

  // TODO: add ticket reply and log GET endpoints
  // api/ticket/1/replies => get all replies for ticket 1
  // api/ticket/1/logs => get all logs for ticket 1

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../lib/session.php');
  require_once(__DIR__ . '/../../database/ticket.php');
  require_once(__DIR__ . '/../../database/user.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 5) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      if (isset($parts[4]) && $parts[4] != 'replies' && $parts[4] != 'logs') {
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

        if (isset($parts[4]) && $parts[4] == 'replies') {
          $replies = $ticket->getReplies();

          $body = [];
          foreach ($replies as $reply) {
            $body[] = $reply->parseJsonInfo();
          }

          API::sendResponse(HttpStatus::OK, $body);
          return;
        }

        if (isset($parts[4]) && $parts[4] == 'logs') {
          $logs = $ticket->getLogs();

          $body = [];
          foreach ($logs as $log) {
            $body[] = $log->parseJsonInfo();
          }

          API::sendResponse(HttpStatus::OK, $body);
          return;
        }

        API::sendResponse(HttpStatus::OK, $ticket->parseJsonInfo());
        return;
      }

      $tickets = Ticket::getAllTickets();

      $body = [];
      foreach ($tickets as $ticket) {
        $body[] = $ticket->parseJsonInfo();
      }

      API::sendResponse(HttpStatus::OK, $body);
      return;
    case RequestMethod::POST:
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not logged in');
        return;
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('title', $data) || !array_key_exists('text', $data) || !array_key_exists('clientId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        return;
      }

      if (gettype($data['title']) != 'string' || gettype($data['text']) != 'string' || gettype($data['clientId']) != 'integer') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (array_key_exists('hashtags', $data) && !is_array($data['hashtags'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (array_key_exists('departmentId', $data) && gettype($data['departmentId']) != 'integer') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      $just_hashtags = array_key_exists('hashtags', $data) && count($data) > 4;
      $just_department = array_key_exists('departmentId', $data) && count($data) > 4;
      $both = array_key_exists('hashtags', $data) && array_key_exists('departmentId', $data) && count($data) > 5;
      if (count($data) > 3 || $just_hashtags || $just_department || $both) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      if (!Client::exists($data['clientId'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Client does not exist');
        return;
      }

      if (isset($data['departmentId']) && !Department::exists($data['departmentId'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
        return;
      }

      $hashtags = isset($data['hashtags']) ? $data['hashtags'] : [];
      $department = isset($data['departmentId']) ? (int) $data['departmentId'] : null;

      $ticket = Ticket::create($data['title'], $data['text'], (int) $data['clientId'], $hashtags, $department);

      API::sendResponse(HttpStatus::CREATED, ['message' => 'Ticket created', 'body' => $ticket->parseJsonInfo()]);
      return;
    case RequestMethod::PUT:
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not logged in');
        return;
      }

      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'JSON body is empty');
        return;
      }

      if (!array_key_exists('title', $data) && !array_key_exists('text', $data) && !array_key_exists('hashtags', $data) && !array_key_exists('agentId', $data) && !array_key_exists('departmentId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        return;
      }

      if (array_diff_key($data, array_flip(['title', 'text', 'hashtags', 'agentId', 'departmentId']))) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      try {
        $ticket = new Ticket((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
        return;
      }

      if (isset($data['title'])) {
        if (gettype($data['title']) != 'string') {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        $ticket->setTitle($data['title']);
      }

      if (isset($data['text'])) {
        if (gettype($data['text']) != 'string') {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        $ticket->setText($data['text']);
      }

      if (isset($data['hashtags'])) {
        if (!is_array($data['hashtags'])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        $ticket->setHashtags($data['hashtags']);
      }

      if (isset($data['agentId']) && !isset($data['departmentId'])) {
        if (gettype($data['agentId']) != 'integer' && gettype($data['agentId']) != 'null') {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        if ($data['agentId'] == null) {
          $ticket->removeAgent();
        } else {
          if (!Agent::exists($data['agentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent does not exist');
            return;
          }

          if ($ticket->getDepartment() == null) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Ticket does not have a department');
            return;
          }
  
          if (!Department::isAgentFromDepartment(new Agent((int) $data['agentId']), $ticket->getDepartment())) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent is not from the tickt\'s department');
            return;
          }
  
          $ticket->assignAgent(new Agent((int) $data['agentId']));
        }
      }

      if (isset($data['departmentId']) && !isset($data['agentId'])) {
        if (gettype($data['departmentId']) != 'integer' && gettype($data['departmentId']) != 'null') {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        if ($data['departmentId'] == null) {
          $ticket->removeDepartment();
        } else {
          if (!Department::exists($data['departmentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
            return;
          }
  
          if (!Department::isAgentFromDepartment($ticket->getAgent(), new Department((int) $data['departmentId']))) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent is not from the desired department');
            return;
          }
  
          $ticket->assignDepartment(new Department((int) $data['departmentId']));
        }
      }

      if (isset($data['agentId']) && isset($data['departmentId'])) {
        if (gettype($data['agentId']) != 'integer' && gettype($data['agentId']) != 'null' && gettype($data['departmentId']) != 'integer' && gettype($data['departmentId']) != 'null') {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        if ($data['departmentId'] == null) {
          $ticket->removeAgent();
          $ticket->removeDepartment();
        } else if ($data['agentId'] == null) {
          if (!Department::exists($data['departmentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
            return;
          }
          
          $ticket->removeAgent();
          $ticket->assignDepartment(new Department((int) $data['departmentId']));
        } else {
          if (!Agent::exists($data['agentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent does not exist');
            return;
          }

          if (!Department::exists($data['departmentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
            return;
          }
  
          if (!Department::isAgentFromDepartment(new Agent((int) $data['agentId']), new Department((int) $data['departmentId']))) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent is not from the desired department');
            return;
          }

          $ticket->removeAgent();
          $ticket->removeDepartment();
  
          $ticket->assignAgent(new Agent((int) $data['agentId']));
          $ticket->assignDepartment(new Department((int) $data['departmentId']));
        }
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket updated successfully',
        'body' => $ticket->parseJsonInfo()
      ]);
      return;
    case RequestMethod::DELETE:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        return;
      }

      if (!is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      try {
        $body = Ticket::delete((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
        return;
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket deleted successfully',
        'body' => $body
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
