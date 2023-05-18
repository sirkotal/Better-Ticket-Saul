<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/hashtag.php');

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
      
      $hashtagDb = new Hashtag();

      // get replies by id
      if (isset($parts[3])) {
        API::sendResponse(HttpStatus::OK, $hashtagDb->parseJsonInfo((int) $parts[3]));
        die();
      }

      $body = [];
      foreach ($hashtagDb->getHashtags() as $id => $hashtag) {
        $body[] = $hashtagDb->parseJsonInfo($id);
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
        API::sendError(HttpStatus::BAD_REQUEST, 'JSON body is empty');
        die();
      }

      if (!array_key_exists('hashtag', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (gettype($data['hashtag']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (array_diff_key($data, array_flip(['hashtag']))) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      $hashtagDb = new Hashtag();

      if (Hashtag::exists($data['hashtag'])) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Hashtag already exists');
        die();
      }

      $body = $hashtagDb->addHashtag($data['hashtag']);

      API::sendResponse(HttpStatus::CREATED, [
        'message' => 'Hashtag created',
        'body' => $body
      ]);
      die();
    case RequestMethod::PUT:
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
      
      $data = API::getJsonInput();

      if (empty($data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'JSON body is empty');
        die();
      }

      if (!array_key_exists('hashtag', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (gettype($data['hashtag']) !== 'string') {
        API::sendError(HttpStatus::BAD_REQUEST, 'Invalid field types');
        die();
      }

      if (array_diff_key($data, array_flip(['hashtag']))) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        die();
      }

      if (!Hashtag::exists((int) $parts[3])) {
        API::sendError(HttpStatus::NOT_FOUND, 'Hashtag not found');
        die();
      }

      $hashtagDb = new Hashtag();
      $body = $hashtagDb->updateHashtag((int) $parts[3], $data['hashtag']);

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Hashtag updated',
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

      $hashtagDb = new Hashtag();
      $body = $hashtagDb->removeHashtag((int) $parts[3]);

      API::sendResponse(HttpStatus::OK, [
        'message' => 'Hashtag deleted',
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
