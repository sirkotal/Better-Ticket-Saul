<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');

  $session = new Session();

  $name = '';
  $email = '';

  if ($session->isLoggedIn()) {
    $user = $session->getUser();
    $name = $user->getName();
    $email = $user->getEmail();
  }
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <section id="contact">
    <h1>Contact Us</h1>
    <form>
      <label>
        <input type="text" name="name" required value="<?= $name ?>" <?php if ($session->isLoggedIn()) echo 'disabled' ?>> <p>Name</p> 
      </label>
      <label>
        <input type="text" name="email" required value="<?= $email ?>" <?php if ($session->isLoggedIn()) echo 'disabled' ?>> <p>Email</p>
      </label>
      <label>
        <input type="text" name="subject" required> <p>Subject</p> 
      </label>
      <label>
        <textarea name="message" required></textarea> <p>Message</p> 
      </label>
      <button formaction="#" formmethod="post">Submit</button>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
