<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../lib/session.php');

  $session = new Session();

  if ($session->isLoggedIn()) {
    $session->logout();
  }

  header('Location: /');
?>