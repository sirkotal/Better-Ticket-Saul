<?php
  declare (strict_types = 1);

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // header('Location: /');
    echo 'not post';
    die();
  }

  require_once(__DIR__ . '/../lib/session.php');

  $session = new Session();

  if ($session->isLoggedIn()) {
    header('Location: /');
    die();
  }

  require_once(__DIR__ . '/../database/user.php');

  $username = $_POST['username'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (User::exists($username)) {
    $session->setError('error-register', 'User already exists');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (User::emailExists($email)) {
    $session->setError('error-register', 'Email already exists');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  if (!User::create($username, $name, $email, $password)) {
    $session->setError('error-register', 'Error creating user');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
  }

  $session->setUser($username);

  header('Location: /');
?>
