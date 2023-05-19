<?php
require_once(__DIR__ . '/../lib/session.php');

$session = new Session();

$isLoggedIn = $session->isLoggedIn();

header('Content-Type: application/json');
echo json_encode(array('isLoggedIn' => $isLoggedIn));
?>