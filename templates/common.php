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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="/style/header.css" rel="stylesheet">
    <link href="/style/footer.css" rel="stylesheet">
    <link href="/style/register.css" rel="stylesheet">
    <link href="/style/login.css" rel="stylesheet">
    <link href="/style/signup.css" rel="stylesheet">
    <link href="/style/responsive.css" rel="stylesheet">
    <link href="/style/layout.css" rel="stylesheet">
    <link href="/style/home.css" rel="stylesheet">
    <link href="/style/departments.css" rel="stylesheet">
    <script src="/script/dropdown.js" defer></script>
    <script src="/script/switch.js" defer></script>
    
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
        <a href="/" id="logo"><img src="/assets/logo.webp" alt="logo" width="75" height = "75" /></a>
        <h1>Better Ticket Saul</h1>
        <nav id="menu" class="menu">
          <ul>
            <li><a href="/index.php">Home</a></li>
            <li><a href="#">FAQs</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </nav>
        <button id="hamburger-button">
            <svg class="hamburger" viewBox="0 0 100 100" width="25">
              <rect class="line top" width="80" height="10" x="10" y="25" rx="5" />
              <rect class="line middle" width="80" height="10" x="10" y="45" rx="5" />
              <rect class="line bottom" width="80" height="10" x="10" y="65" rx="5" />
            </svg>
        </button>
        <?php if ($session->isLoggedIn()) { ?>
          <div class="dropdown">
            <button id="ticket-button" class="header-options">Ticket</button>
            <div id="ticket-menu" class="dropdown-menu">
              <ul>
                <li><a href="/create_ticket.php">Submit Ticket</a></li>
                <li><a href="/view_ticket.php">View Tickets</a></li>
              </ul>
            </div>
          </div>
          <button id=logout><a href="/actions/action_logout.php">Logout</a></button>
          <div id="hamburger-menu" class="dropdown-menu">
            <ul>
              <li><a href="/index.php">Home</a></li>
              <li><a href="/create_ticket.php">Submit Ticket</a></li>
              <li><a href="/view_ticket.php">View Tickets</a></li>
              <li><a href="#">FAQs</a></li>
              <li><a href="#">Contact</a></li>
              <li><a href="/actions/action_logout.php">Logout</a></li>
            </ul>
          </div>
        <?php } else { ?>
          <div id='sign'>
            <button id='signin'><a href="/login.php">Login</a></button>
            <button id='signup'><a href="/register.php">Register</a></button>
          </div>
          <div id="hamburger-menu" class="dropdown-menu">
            <ul id = "hamburger-sign">
              <li><a href="/index.php">Home</a></li>
              <li><a href="#">FAQs</a></li>
              <li><a href="#">Contact</a></li>
              <li><a href="/login.php">Login</a></li>
            </ul>
          </div>
        <?php } ?>
  </header>
  
<?php } ?>

<?php function outputFooter(): void { ?>
  <footer>
    <p>&copy; 2023 Better Ticket Saul || All Rights Reserved</p>
    <div class="footer-icons">
        <ul class="socials">
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
        </ul>
    </div>
  </footer>
<?php } ?>
