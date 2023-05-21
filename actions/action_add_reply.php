<?php
  declare (strict_types = 1);

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    die();
  }

  require_once(__DIR__ . '/../lib/session.php');

  $session = new Session();

  if (!$session->isLoggedIn()) {
    header('Location: /');
    die();
  }

  $reply = $_POST['reply'];
  $ticketId = $_POST['ticket_id'];

  if (empty($reply)) {
    $session->setError('error-reply', 'Reply field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  require_once(__DIR__ . '/../database/ticket_reply.php');

  $ticketReply = TicketReply::create($reply, (int) $ticketId, (int) $session->getUser()->getId());

  $session->unsetError('error-reply');
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  die();
?>