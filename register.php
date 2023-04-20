<?php
  declare (strict_types = 1);

  require_once('/templates/common.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trouble Ticket Management System</title>
</head>
<body>
  <?php outputHeader() ?>
  <section id="register">
    <h1>Register</h1>
    <form>
      <label>
        Username <input type="text" name="username">
      </label>
      <label>
        Name <input type="text" name="realname">
      </label>
      <label>
        E-mail <input type="email" name="email">
      </label>
      <label>
        Password <input type="password" name="password">
      </label>
      <button formaction="#" formmethod="post">Register</button>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
