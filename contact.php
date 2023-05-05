<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <main id="faq">
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
        <input type="textarea" name="message" required> <p>Message</p> 
      </label>
      <button type="submit">Submit</button>
    </form>
  </main>
  <?php outputFooter() ?>
</body>
</html>
