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

  if (!isset($_POST['csrf']) || !$session->getCsrf() != $_POST['csrf']) {
    header('Location: /');
    die();
  }

  $username = $_POST['username'];
  $password = $_POST['password'];

  if (empty($username)) {
    $session->setError('error-register', 'Username field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (empty($password)) {
    $session->setError('error-register', 'Password field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  require_once(__DIR__ . '/../database/user.php');

  if (!User::exists($username)) {
    $session->setError('error-login', 'User does not exist');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  $user_id = User::isValid($username, $password);
  if ($user_id === false) {
    $session->setError('error-login', 'Invalid password');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  $session->setUser($user_id);
  $session->unsetError('error-login');
  header('Location: /');
?>