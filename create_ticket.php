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
  <section id="open">
    <h1>Open a Ticket</h1>
    <form>
      <label>
        Ticket Title <input type="text" name="title">
      </label>
      <label>
        Add your hashtags <input type="text" name="hashtags">
      </label>
      <div class="hash-container"></div>
      <button formaction="#" formmethod="post">Open</button>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>
