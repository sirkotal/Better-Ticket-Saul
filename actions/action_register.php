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

  require_once(__DIR__ . '/../database/user.php');

  $username = $_POST['username'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($username)) {
    $session->setError('error-register', 'Username field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (empty($name)) {
    $session->setError('error-register', 'Name field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (empty($email)) {
    $session->setError('error-register', 'Email field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (empty($password)) {
    $session->setError('error-register', 'Password field is empty');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (User::exists($username)) {
    $session->setError('error-register', 'User already exists');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (User::emailExists($email)) {
    $session->setError('error-register', 'Email already in use');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  $user = User::create($username, $name, $email, $password);
  $session->setUser($user->getId());
  $session->unsetError('error-register');

  header('Location: /');
?>