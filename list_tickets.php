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
  
  $client = $session->getUser();
  
  require_once(__DIR__ . '/database/ticket.php');
  
  $tickets = Ticket::getTicketsByClient($client);

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
        $ticket_department = $ticket->getDepartment() === null ? 'No department' : $ticket->getDepartment()->getName();
        $ticket_agent = $ticket->getAgent() === null ? 'No agent' : $ticket->getAgent()->getName(); ?>
        
        <div class="ticket" data-id="<?= $ticket_id ?>">
          <h2 class="title"><a href="#"><?= $ticket_title ?></a></h2>
          <div class="bottom-row">
            <p>Status:<span class="status" data-color="<?= TicketStatus::getColor($ticket->getStatus())['color'] ?>"><?= $ticket_status ?></span></p>
            <p class="department"><?= $ticket_department ?></p>
            <p class="agent"><?= $ticket_agent ?></p>
            <p class="date"><?= date('F j Y', $ticket_date) ?></p>
          </div>
        </div>
    <?php } ?>
  </div>
</body>
<?php outputFooter() ?>
