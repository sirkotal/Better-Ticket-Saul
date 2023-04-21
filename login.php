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
        Username <input type="text" name="username">
      </label>
      <label>
        Password <input type="password" name="password">
      </label>
      <button formaction="#" formmethod="post">Login</button>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
