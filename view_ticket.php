<?php
  declare (strict_types = 1);

  require_once('/templates/common.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trouble Ticket Management System</title>
</head>
<body>
  <?php outputHeader() ?>
  <section id="ticket"> <!--questions here-->
    <span class="ticketTitle">Do You Smell What The Rock is Cooking</span>
    <span class="ticketName">The Rock</span>
    <span class="ticketDate">2002</span>
    <span class="ticketPriority">High</span>
    <span class="ticketHash">#amogus</span>
    <span class="ticketHash">#fortnite</span>
    <span class="ticketHash">#faceoff</span>
    <span class="ticketHash">#tequila</span>
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