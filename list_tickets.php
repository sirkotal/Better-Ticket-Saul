<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');

  $session = new Session();

  if (!$session->isLoggedIn()) {
    header('Location: login.php');
    die();
  }

  require_once(__DIR__ . '/database/user.php');
  
  $client = new Client($session->getUser());
  
  require_once(__DIR__ . '/database/ticket.php');
  
  $tickets = $client->getAllTickets();

  // TODO: format html to display ticket info
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <div id="tickets">
    <h1>My Tickets:</h1>
    <?php
      foreach ($tickets as $ticket) { //? refactor this to a different file
        $ticket_id = $ticket->getId();
        $ticket_title = $ticket->getTitle();
        $ticket_text = $ticket->getText();
        $ticket_date = $ticket->getDate();
        $ticket_status = $ticket->getStatus();
        $ticket_department = $ticket->getDepartment() === null ? 'No department' : $ticket->getDepartment()->getName(); ?>
        
        <div class="ticket" data-id="<?= $ticket_id ?>">
          <h2 class="title"><?= $ticket_title ?></h2>
          <p class="date"><?= date('F j Y', $ticket_date) ?></p>
          <p class="status" data-color='#008000'><?= $ticket_status ?></p>
          <p class="department"><?= $ticket_department ?></p>
        </div>
    <?php } ?>
  </div>
</body>
<?php outputFooter() ?>
