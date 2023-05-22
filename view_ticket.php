<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');

  $url = parse_url($_SERVER['REQUEST_URI']);
  $queries_str = $url['query'];

  if ($queries_str === null) {
    header('Location: /list_tickets.php');
    die();
  }

  parse_str($queries_str, $queries);
  if (!isset($queries['id'])) {
    header('Location: /list_tickets.php');
    die();
  }

  $ticket_id = $queries['id'];

  if (!is_numeric($ticket_id)) {
    header('Location: /list_tickets.php');
    die();
  }

  require_once(__DIR__ . '/lib/session.php');
  require_once(__DIR__ . '/database/user.php');
  require_once(__DIR__ . '/database/department.php');
  require_once(__DIR__ . '/database/ticket.php');

  $session = new Session();
  $user = $session->getUser();
  $ticket = new Ticket((int) $ticket_id);

  if (!$session->isLoggedIn()) {
    header('Location: /login.php');
    die();
  }

  if ($ticket->getClient()->getId() !== $user->getId() && !User::isAdmin($user->getId()) && !Department::isAgentFromDepartment(new Agent($user->getId()), $ticket->getDepartment())) {
    header('Location: /list_tickets.php');
    die();
  }

  require_once(__DIR__ . '/database/ticket_reply.php');
?>

<?php function outputReply(TicketReply $reply) { ?>
  <article class="reply">
    <div class="info">
      <p class="user"><?= $reply->getAuthor()->getName() ?></p>
      <p class="date"><?= date('F j Y', $reply->getDate()) ?></p>
    </div>
    <p class="text"><?= $reply->getReply() ?></p>
  </article>
<?php } ?>

<?php outputHead($stylesheets = [
    '/style/view_ticket.css'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <section id="ticket">
    <h1 class="title"><?= $ticket->getTitle() ?></h1>
    <div class="ticket-info">
      <p class="client"><?= $ticket->getClient()->getName() ?></p>
      <p class="date"><?= date('F j Y', $ticket->getDate()) ?></p>
      <p class="status"><?= $ticket->getStatus() ?></p>
      <p class="department"><?= $ticket->getDepartment() !== null ? $ticket->getDepartment()->getName() : 'No Department' ?></p>
      <p class="agent"><?= $ticket->getAgent() !== null ? $ticket->getAgent()->getName() : 'No Agent' ?></p>
    </div>
    <ul class="hashtags">
      <?php foreach($ticket->getHashtags() as $hashtag) { ?>
        <li><?= $hashtag ?></li>
      <?php } ?>
    </ul>
    <p class="text"><?= $ticket->getText() ?></p>
  </section>
  <?php if (User::isAdmin($user->getId()) || $ticket->getClient()->getId() === $user->getId() || $ticket->getAgent()->getId() === $user->getId()) { ?>
    <form class="add-comment" method="post" action="/actions/action_add_reply.php">
      <label for="reply">Reply:</label>
      <textarea id="reply" name="reply" rows="4" cols="50"></textarea>
      <input class="submit-reply" type="submit" value="Submit">
      <input type="hidden" name="ticket_id" value="<?= $ticket->getId() ?>">
    </form>
  <?php } ?>
  <section id="replies">
    <h1>Replies:</h1>
    <?php foreach($ticket->getReplies() as $reply) { ?>
      <?php outputReply($reply) ?>
    <?php } ?>
  </section>  
  <?php outputFooter() ?>
</body>
</html>