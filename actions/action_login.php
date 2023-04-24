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

  $username = $_POST['username'];
  $password = $_POST['password'];

  require_once(__DIR__ . '/../database/user.php');

  if (!User::exists($username)) {
    $session->setError('error-login', 'User does not exist');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (!User::isValid($username, $password)) {
    $session->setError('error-login', 'Invalid password');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  $session->setUser($username);
  header('Location: /');
?>
