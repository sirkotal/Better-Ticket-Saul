<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../lib/session.php');
?>

<?php function outputHead(
  array $stylesheets = [],
  array $scripts = []
): void { ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trouble Ticket Management System</title>
    <link href="style/header.css" rel="stylesheet">
    <link href="style/footer.css" rel="stylesheet">

    <?php foreach ($stylesheets as $stylesheet) { ?>
      <link rel="stylesheet" href="<?= $stylesheet ?>">
    <?php } ?>

    <?php foreach ($scripts as $script) { ?>
      <script src="<?= $script ?>" defer></script>
    <?php } ?>

  </head>
<?php } ?>

<?php function outputHeader(): void {
  $session = new Session(); ?>
  <header>
      <img src="/assets/logo.svg" id= "logo" alt="logo" width="130" height = "100" />
      <div id="signup">
        <?php if ($session->isLoggedIn()) { ?>
          <!-- Add a action to logout -->
          <a href="/actions/action_logout.php">Logout</a>
        <?php } else { ?>
          <a href="/register.php">Register</a>
          <a href="/login.php">Login</a>
        <?php } ?>
    </div>
    <h1><a href="/index.php">Trouble Ticket Management System</a></h1>
    <nav id="menu">
      <ul>
        <li><a href="/index.php">Home</a></li>
        <li><a href="/create_ticket.php">Submit Ticket</a></li>
        <li><a href="/view_ticket.php">View Tickets</a></li>
        <li><a href="#">FAQs</a></li>
        <li><a href="#">Contact Us</a></li>
      </ul>
    </nav>
  </header>
<?php } ?>

<?php function outputFooter(): void { ?>
  <footer>
    <p>&copy; 2023 The LDTS Group || All Rights Reserved</p>
  </footer>
<?php } ?>
