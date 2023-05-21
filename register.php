<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');

  $session = new Session();
  $error = $session->getError('error-register');
  $session->unsetError('error-register');
?>

<?php outputHead(
  $stylesheets = [
    $error !== null ? '/style/errors.css': '',
    '/style/register.css'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <section id="register">
    <h1>Register</h1>
    <form method="post" action="/actions/action_register.php">
      <label>
        <input type="text" name="username"> <p>Username</p>
      </label>
      <label>
        <input type="text" name="name"> <p>Name</p> 
      </label>
      <label>
        <input type="email" name="email"> <p>E-mail</p> 
      </label>
      <label>
        <input type="password" name="password"> <p>Password</p> 
      </label>
      <?php if ($error !== null) { ?>
        <p class="input-error"><?= $error ?></p>
      <?php } ?>
      <input type="hidden" name="csrf" value="<?= $session->getCsrf() ?>">
      <button type="submit">Register</button>
      <a href="/login.php">Log in</a>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
