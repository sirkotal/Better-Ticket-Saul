<?php
require_once(__DIR__ . '/../lib/session.php');

$session = new Session();

if ($session->isLoggedIn()) {
  echo json_encode(['name' => $session->getUser()]);
} 
else {
  http_response_code(401);
  echo json_encode(['error' => 'User is not logged in']);
}
?>