<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');

  $session = new Session();

  if (!$session->isLoggedIn()) {
    header('Location: login.php');
    die();
  }
?>

<?php outputHead(
  $stylesheets = [
    '/style/list_tickets.css'
  ],
  $scripts = [
    '/script/list_tickets.js'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <div id="tickets">
    <h1>My Tickets:</h1>
  </div>
</body>
<?php outputFooter() ?>
