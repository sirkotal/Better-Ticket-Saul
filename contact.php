<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <?php 
  $name = isset($_SESSION['user']) ? $_SESSION['user'] : '';
  $email = isset($_SESSION['user']) ? $_SESSION['email'] : '';
  // var_dump($email);
  ?>
  <section id="contact">
    <h1>Contact Us</h1>
    <form>
      <label>
        <input type="text" name="name" required value="<?php echo $name; ?>"> <p>Name</p> 
      </label>
      <label>
        <input type="text" name="email" required value="<?php echo $email; ?>"> <p>Email</p> 
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
