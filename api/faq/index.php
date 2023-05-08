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
          $question = $faq->getQuestionById((int) $parts[3]);
        } catch (Exception $e) {
          API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
          return;
        }

        $body = [
          'id' => $question['id'],
          'question' => $question['question'],
          'answer' => $question['answer']
        ];

        API::sendGetResponse(HttpStatus::OK, $body);
        return;
      }

      $questions = $faq->getQuestions();

      $body = [];

      foreach ($questions as $question) {
        $body[] = [
          'id' => $question['id'],
          'question' => $question['question'],
          'answer' => $question['answer']
        ];
      }

      API::sendGetResponse(HttpStatus::OK, $body);
      return;
    case RequestMethod::POST:
      $json_data = file_get_contents('php://input');
      $data = json_decode($json_data, true);

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

      $faq->addQuestion($data['question'], $data['answer']);

      API::sendPostResponse(HttpStatus::CREATED, ['message' => 'Question added to FAQ']);
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
        $question = $faq->removeQuestion((int) $parts[3]);
      } catch (Exception $e) {
        API::sendError(HttpStatus::NOT_FOUND, 'Question not found');
        return;
      }

      API::sendDeleteResponse(HttpStatus::OK, [
        'message' => 'Question removed from FAQ',
        'question' => $question
      ]);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
