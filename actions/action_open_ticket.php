<?php
  declare (strict_types = 1);

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    die();
  }

  require_once(__DIR__ . '/../lib/session.php');

  $session = new Session();

  if ($session->isLoggedIn()) {
    header('Location: /');
    die();
  }

  $title = $_POST['title'];
  $text = $_POST['text'];
  $date = $_POST['date'];
  $status = $_POST['status'];
  $clientId = $_POST['clientId'];

  require_once(__DIR__ . '/../database/ticket.php');

  $ticketReply = Ticket::create($title, $text, $date, $status, $clientId);

  $session->unsetError('error-ticket');
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  die();
?>