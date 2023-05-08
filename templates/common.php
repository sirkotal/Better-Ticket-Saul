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
    
    <link href="/style/edit_profile.css" rel="stylesheet">
    <link href="/style/header.css" rel="stylesheet">
    <link href="/style/footer.css" rel="stylesheet">
    <link href="/style/register.css" rel="stylesheet">
    <link href="/style/login.css" rel="stylesheet">
    <link href="/style/signup.css" rel="stylesheet">
    <link href="/style/responsive.css" rel="stylesheet">
    <link href="/style/contact.css" rel="stylesheet">
    <link href="/style/faq.css" rel="stylesheet">
    <link href="/style/layout.css" rel="stylesheet">
    <link href="/style/home.css" rel="stylesheet">
    <link href="/style/departments.css" rel="stylesheet">
    <link href="/style/list_tickets.css" rel="stylesheet">
    <script src="/script/dropdown.js" defer></script>
    <script src="/script/switch.js" defer></script>
    <script src="/script/editprofile.js" defer></script>
    <script src="/script/dropdown.js" defer></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <?php foreach ($stylesheets as $stylesheet) { ?>
      <?php if (!empty($stylesheet)) { ?>
        <link rel="stylesheet" href="<?= $stylesheet ?>">
      <?php } ?>
    <?php } ?>

    <?php foreach ($scripts as $script) { ?>
      <?php if (!empty($script)) { ?>
        <script src="<?= $script ?>" defer></script>
      <?php } ?>
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
            <li><a href="/faq.php">FAQs</a></li>
            <li><a href="/contact.php">Contact Us</a></li>
            <?php if ($session->isLoggedIn()) { ?>
              <li><a href="/departments.php">Departments</a></li>
            <?php } ?>  
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
                <li><a href="/list_tickets.php">View Tickets</a></li>
                <li><a href="/edit_profile.php">Edit Profile</a></li>
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
            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
        </ul>
    </div>
  </footer>
<?php } ?>

<?php function outputDropdownButton(): void { ?>
  <button class="dropdown-button" aria-expanded="false">
		<span class="down-button"><i class='far fa-caret-square-down'></i></span>
	</button>
<?php } ?>
