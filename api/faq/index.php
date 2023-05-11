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
        return;
      }

      // get question by id
      if ($parts[3]) {
        if (!is_numeric($parts[3])) {
          API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
          return;
        }

        try {
          $faq->getQuestionById((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
          return;
        }

        API::sendResponse(HttpStatus::OK, FAQ::parseJsonInfo((int) $parts[3]));
        return;
      }

      $questions = $faq->getQuestions();

      $body = [];

      foreach ($questions as $question) {
        $index = array_search($question, $questions);
        
        $body[] = FAQ::parseJsonInfo($index);
      }

      API::sendResponse(HttpStatus::OK, $body);
      return;
    case RequestMethod::POST:
      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Body is empty');
        return;
      }

      if (!array_key_exists('question', $data) || !array_key_exists('answer', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required fields');
        return;
      }

      if (gettype($data['question']) !== 'string' || gettype($data['answer']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        return;
      }

      if (count($data) > 2) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      $body = $faq->addQuestion($data['question'], $data['answer']);

      API::sendResponse(HttpStatus::CREATED, ['message' => 'Question added to FAQ', 'body' => $body]);
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
        $body = $faq->removeQuestion((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
        return;
      }

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Question removed from FAQ',
        'body' => $body
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
