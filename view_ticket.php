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
?>

<?php outputHead($stylesheets = [
    '/style/view_ticket.css'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <section id="ticket"> <!--questions here-->
    <span class="ticketTitle">Do You Smell What The Rock is Cooking</span>
    <!-- <span class="ticketText">The Rock is cooking something can you smell it?</span> -->
    <span class="ticketName">The Rock</span>
    <span class="ticketDate">2002</span>
    <span class="ticketStatus">In progress</span>
    <ul class="ticketHashtags">
        <li><a href=#>#amogus</a></li>
        <li><a href=#>#fortnite</a></li>
        <li><a href=#>#faceoff</a></li>
        <li><a href=#>#tequila</a></li>
    </ul>
  </section>  
  <section id="messages">
    <article class="message">
      <span class="user">The Rock</span>
      <span class="date">1984</span>
      <p>Something Something Something</p>  <!--would <div> work well here?-->
    </article>
  </section>  
  <?php outputFooter() ?>
</body>
</html>