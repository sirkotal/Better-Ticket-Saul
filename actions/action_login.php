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

  require_once(__DIR__ . '/../database/user.php');

  if (User::exists($_POST['username'], $_POST['password'])) {
    $session->setUserUsername($_POST['username']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

  // TODO: error handling
  header('Location: /');
?>