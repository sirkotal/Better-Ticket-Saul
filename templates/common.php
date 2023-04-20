<?php
  declare (strict_types = 1);
?>

<?php function outputHeader(): void { ?>
  <header>
    <img src="<?= '/assets/logo.png' ?>" alt="logo" />
    <h1><a href="<?= '/index.php' ?>">Trouble Ticket Management System</a></h1>
    <div id="signup">
      <a href="<?= '/register.php' ?>">Register</a>
      <a href="<?= '/login.php' ?>">Login</a>
    </div>
    <nav id="menu">
      <ul>
        <li><a href="<?= '/index.php' ?>">Home</a></li>
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
