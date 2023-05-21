<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead($stylesheets = [
    '/style/view_ticket.css'
  ]
  ) ?>
<body>
  <?php outputHeader() ?>
  <section id="ticket"> <!--questions here-->
    <span class="ticketTitle">Do You Smell What The Rock is Cooking</span>
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