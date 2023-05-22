<?php
  declare(strict_types=1);

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
        die();
      }

      if (isset($parts[4]) && $parts[4] != 'replies' && $parts[4] != 'logs') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      $session = new Session();
      $user = $session->getUser();

      // get ticket by id
      if ($parts[3]) {
        if (!is_numeric($parts[3])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          die();
        }

        try {
          $ticket = new Ticket((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
          die();
        }

        if ($ticket->getClient()->getId() != $user->getId() || (User::isAgent($user->getId()) && Department::isAgentFromDepartment(new Agent($user->getId()), $ticket->getDepartment()))) {
          API::sendError(HttpStatus::FORBIDDEN, 'You do not have permission to do that');
          die();
        }

        if (isset($parts[4]) && $parts[4] == 'replies') {
          $replies = $ticket->getReplies();

          $body = [];
          foreach ($replies as $reply) {
            $body[] = $reply->parseJsonInfo();
          }

          API::sendResponse(HttpStatus::OK, $body);
          die();
        }

        if (isset($parts[4]) && $parts[4] == 'logs') {
          $logs = $ticket->getLogs();

          $body = [];
          foreach ($logs as $log) {
            $body[] = $log->parseJsonInfo();
          }

          API::sendResponse(HttpStatus::OK, $body);
          die();
        }

        API::sendResponse(HttpStatus::OK, $ticket->parseJsonInfo());
        die();
      }

      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not logged in');
        die();
      }

      if (User::isAdmin($user->getId())) {
        $tickets = Ticket::getAllTickets();

        $body = [];
        foreach ($tickets as $ticket) {
          $body[] = $ticket->parseJsonInfo();
        }

        API::sendResponse(HttpStatus::OK, $body);
        die();
      }

      if (User::isAgent($user->getId())) {
        $ticketsAll = Ticket::getAllTickets();
        $tickets = [];

        $departments = $user->getDepartments();
        foreach ($departments as $department) {
          foreach ($ticketsAll as $ticket1){
            if ($ticket1->getDepartment()!==null && $ticket1->getDepartment()->getId() == $department->getId())
              array_push($tickets, $ticket1);
          }
        }
        $own_tickets = Ticket::getTicketsByClient($user);
        foreach ($own_tickets as $ticket){
          $flag = true;
          foreach ($tickets as $t){
            if ($t->getId() == $ticket->getId()){
              $flag = false;
              break;
            }
          }
          if ($flag)
            array_push($tickets, $ticket);
        }
        $tickets = array_merge($tickets, $own_tickets);
        $body = [];
        foreach ($tickets as $ticket) {
          $body[] = $ticket->parseJsonInfo();
        }

        API::sendResponse(HttpStatus::OK, $body);
        die();
      }

      // is not admin and is not agent -> is client
      if (!User::isAgent($user->getId())) {
        $tickets = Ticket::getTicketsByClient($user);

        $body = [];
        foreach ($tickets as $ticket) {
          $body[] = $ticket->parseJsonInfo();
        }

        API::sendResponse(HttpStatus::OK, $body);
        die();
      }

      API::sendError(HttpStatus::FORBIDDEN, 'You do not have permission to do that');
      die();
    case RequestMethod::POST:
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not logged in');
        die();
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        die();
      }

      if (!array_key_exists('title', $data) || !array_key_exists('text', $data) || !array_key_exists('clientId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        die();
      }

      if (gettype($data['title']) != 'string' || gettype($data['text']) != 'string' || gettype($data['clientId']) != 'integer') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (array_key_exists('hashtags', $data) && !is_array($data['hashtags'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (array_key_exists('departmentId', $data) && gettype($data['departmentId']) != 'integer') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      $required = array_key_exists('title', $data) && array_key_exists('text', $data) && array_key_exists('clientId', $data) && !array_key_exists('hashtags', $data) && !array_key_exists('departmentId', $data) && count($data) > 3;
      $just_hashtags = array_key_exists('hashtags', $data) && !array_key_exists('departmentId', $data) && count($data) > 4;
      $just_department = !array_key_exists('hashtags', $data) && array_key_exists('departmentId', $data) && count($data) > 4;
      $both = array_key_exists('hashtags', $data) && array_key_exists('departmentId', $data) && count($data) > 5;
      if ($required || $just_hashtags || $just_department || $both) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      if (!Client::exists($data['clientId'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Client does not exist');
        die();
      }

      if (isset($data['departmentId']) && !Department::exists($data['departmentId'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
        die();
      }

      $hashtags = isset($data['hashtags']) ? $data['hashtags'] : [];
      $department = isset($data['departmentId']) ? (int) $data['departmentId'] : null;

      $ticket = Ticket::create($data['title'], $data['text'], (int) $data['clientId'], $hashtags, $department);

      API::sendResponse(HttpStatus::CREATED, ['message' => 'Ticket created', 'body' => $ticket->parseJsonInfo()]);
      die();
    case RequestMethod::PUT:
      $session = new Session();
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You are not logged in');
        die();
      }

      $user = User::getUserById($session->getUser()->getId());

      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      if (!isset($parts[3]) || !is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'JSON body is empty');
        die();
      }

      if (!array_key_exists('title', $data) && !array_key_exists('text', $data) && !array_key_exists('hashtags', $data) && !array_key_exists('agentId', $data) && !array_key_exists('departmentId', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        die();
      }

      if (array_diff_key($data, array_flip(['title', 'text', 'hashtags', 'agentId', 'departmentId']))) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      try {
        $ticket = new Ticket((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
        die();
      }

      if (!User::isAdmin($user->getId()) || $ticket->getClient()->getId() != $user->getId() || $ticket->getAgent()->getId() != $user->getId()) {
        API::sendError(HttpStatus::FORBIDDEN, 'You do not have permission to do that');
        die();
      }

      if ((isset($data['title']) && gettype($data['title']) != 'string') ||
        (isset($data['text']) && gettype($data['text']) != 'string') ||
        (isset($data['hashtags']) && !is_array($data['hashtags'])) ||
        ((isset($data['agentId']) && gettype($data['agentId']) != 'integer' && gettype($data['agentId']) != 'NULL')) ||
        (isset($data['departmentId']) && gettype($data['departmentId']) != 'integer' && gettype($data['departmentId']) != 'NULL'))
      {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (isset($data['title'])) {
        $ticket->setTitle($data['title']);
      }

      if (isset($data['text'])) {
        $ticket->setText($data['text']);
      }

      if (isset($data['hashtags'])) {
        $ticket->setHashtags($data['hashtags']);
      }

      if (isset($data['agentId']) && !isset($data['departmentId'])) {
        if ($data['agentId'] == null) {
          $ticket->removeAgent();
        } else {
          if (!Agent::exists($data['agentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent does not exist');
            die();
          }

          if ($ticket->getDepartment() == null) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Ticket does not have a department');
            die();
          }
  
          if (!Department::isAgentFromDepartment(new Agent((int) $data['agentId']), $ticket->getDepartment())) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent is not from the tickt\'s department');
            die();
          }
  
          $ticket->assignAgent(new Agent((int) $data['agentId']));
        }
      }

      if (isset($data['departmentId']) && !isset($data['agentId'])) {
        if ($data['departmentId'] == null) {
          $ticket->removeDepartment();
        } else {
          if (!Department::exists($data['departmentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
            die();
          }
  
          if (!Department::isAgentFromDepartment($ticket->getAgent(), new Department((int) $data['departmentId']))) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent is not from the desired department');
            die();
          }
  
          $ticket->assignDepartment(new Department((int) $data['departmentId']));
        }
      }

      if (isset($data['agentId']) && isset($data['departmentId'])) {
        if ($data['departmentId'] == null) {
          $ticket->removeAgent();
          $ticket->removeDepartment();
        } else if ($data['agentId'] == null) {
          if (!Department::exists($data['departmentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
            die();
          }
          
          $ticket->removeAgent();
          $ticket->assignDepartment(new Department((int) $data['departmentId']));
        } else {
          if (!Agent::exists($data['agentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent does not exist');
            die();
          }

          if (!Department::exists($data['departmentId'])) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Department does not exist');
            die();
          }
  
          if (!Department::isAgentFromDepartment(new Agent((int) $data['agentId']), new Department((int) $data['departmentId']))) {
            API::sendError(HttpStatus::BAD_REQUEST, 'Agent is not from the desired department');
            die();
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
      die();
    case RequestMethod::DELETE:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) != 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      if (!is_numeric($parts[3])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      $session = new Session();
      $user = $session->getUser();
      $ticket = new Ticket((int) $parts[3]);

      if (!User::isAdmin($user->getId()) || $ticket->getClient()->getId() != $user->getId() || $ticket->getAgent()->getId() != $user->getId()) {
        API::sendError(HttpStatus::FORBIDDEN, 'You do not have permission to do that');
        die();
      }

      try {
        $body = Ticket::delete((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Ticket not found');
        die();
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Ticket deleted successfully',
        'body' => $body
      ]);
      die();
    case RequestMethod::OPTIONS:
      API::corsSetup(RequestMethod::GET, RequestMethod::POST, RequestMethod::PUT, RequestMethod::DELETE, RequestMethod::OPTIONS);
      die();
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
  }
?>
