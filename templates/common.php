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
    
    <link href="/style/header.css" rel="stylesheet">
    <link href="/style/footer.css" rel="stylesheet">
    <link href="/style/register.css" rel="stylesheet">
    <link href="/style/login.css" rel="stylesheet">
    <script src="/script/hamburger.js" defer></script>

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
      <div id="header-left">
        <a href="/" id="logo"><img src="/assets/logo.svg" alt="logo" width="100" height = "100" /></a>
        <h1><a href="/">Trouble Ticket Management System</a></h1>
        <nav id="menu">
          <ul>
            <li><a href="#">FAQs</a></li>
            <li><a href="#">Contact Us</a></li>
          </ul>
        </nav>
      </div>
      <div id="signup">
        <?php if ($session->isLoggedIn()) { ?>
          <button id="hamburger-button">
            <svg class="hamburger" viewBox="0 0 100 100" width="25">
              <rect class="line top" width="80" height="10" x="10" y="25" rx="5" />
              <rect class="line middle" width="80" height="10" x="10" y="45" rx="5" />
              <rect class="line bottom" width="80" height="10" x="10" y="65" rx="5" />
            </svg>
          </button>
          <div id="hamburger-menu">
            <ul>
              <li><a href="/create_ticket.php">Submit Ticket</a></li>
              <li><a href="/view_ticket.php">View Tickets</a></li>
              <li><a href="/actions/action_logout.php">Logout</a></li>
            </ul>
          </div>
        <?php } else { ?>
          <button><a href="/login.php">Login</a></button>
          <button><a href="/register.php">Register</a></button>
        <?php } ?>
      </div>
  </header>
  
<?php } ?>

<?php function outputFooter(): void { ?>
  <footer>
    <p>&copy; 2023 The LDTS Group || All Rights Reserved</p>
  </footer>
<?php } ?>
