<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <section id="login">
    <h1>Login</h1>
    <form>
      <label>
        <input type="text" name="username"> <p>Username</p> 
      </label>
      <label>
        <input type="password" name="password"> <p>Password</p> 
      </label>
      <button formaction="/actions/action_login.php" formmethod="post">Login</button>
      <a href="/register.php">Sign up</a>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
