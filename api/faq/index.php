<?php
  declare(strict_types=1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/faq.php');

  $faq = new FAQ();

  switch($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $url = parse_url($_SERVER['REQUEST_URI']);
      $parts = explode('/', $url['path']);

      if (count($parts) > 4) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Endpoint not found');
        die();
      }

      // get question by id
      if ($parts[3]) {
        if (!is_numeric($parts[3])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          die();
        }

        try {
          $faq->getQuestionById((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
          die();
        }

        API::sendResponse(HttpStatus::OK, FAQ::parseJsonInfo((int) $parts[3]));
        die();
      }

      $questions = $faq->getQuestions();

      $body = [];

      foreach ($questions as $question) {
        $index = array_search($question, $questions);
        
        $body[] = FAQ::parseJsonInfo($index);
      }

      API::sendResponse(HttpStatus::OK, $body);
      die();
    case RequestMethod::POST:
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

      if (!array_key_exists('question', $data) || !array_key_exists('answer', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        die();
      }

      if (gettype($data['question']) !== 'string' || gettype($data['answer']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      $body = $faq->addQuestion($data['question'], $data['answer']);

      API::sendResponse(HttpStatus::CREATED, ['message' => 'Question added to FAQ', 'body' => $body]);
      die();
    case RequestMethod::PUT:
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

      if (!array_key_exists('question', $data) || !array_key_exists('answer', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        die();
      }

      if (gettype($data['question']) !== 'string' || gettype($data['answer']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      try {
        $body = $faq->updateQuestion((int) $parts[3], $data['question'], $data['answer']);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
        die();
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Question updated',
        'body' => $body
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
      if (!$session->isLoggedIn()) {
        API::sendError(HttpStatus::UNAUTHORIZED, 'You must be logged in to do that');
        die();
      }

      if (!User::isAdmin($session->getUser()->getId())) {
        API::sendError(HttpStatus::FORBIDDEN, 'You must be an admin to do that');
        die();
      }

      try {
        $body = $faq->removeQuestion((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
        die();
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Question removed from FAQ',
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
