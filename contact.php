<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <section id="contact">
    <h1>Contact Us</h1>
    <form>
      <label>
        <input type="text" name="name" required> <p>Name</p> 
      </label>
      <label>
        <input type="text" name="email" required> <p>Email</p> 
      </label>
      <label>
        <input type="text" name="subject" required> <p>Subject</p> 
      </label>
      <label>
        <textarea name="message" required></textarea> <p>Message</p> 
      </label>
      <button type="submit">Submit</button>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
