<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');

  $session = new Session();
?>

<?php outputHead(
  $stylesheets = [
    '/style/home.css'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <!-- TODO: change what to on the index page -->
  <main>
    <div class="saul-container">
      <div class="saul-text">
        <?php if ($session->isLoggedIn()) { ?>
          <h2 id="saul-title">Hello There, <?=$session->getUser()->getName()?>!</h2>
          <p id="saul-greeting">
            Our system allows you to submit and track trouble tickets related to technical issues, 
            customer support, or any other inquiries. Please use the links above to submit a ticket, view your tickets, check the FAQs,
            or contact us for assistance.
          </p>
        <?php } else { ?>
          <h2 id="saul-title">Welcome to Better Ticket Saul</h2>
          <p id="saul-greeting">
            Our system allows you to submit and track trouble tickets related to technical issues, customer support,
            or any other inquiries. Please use the links above to submit a ticket, view your tickets, check the FAQs,
            or contact us for assistance.
          </p>
        <?php } ?>
      </div>
    </div>
  </main>
  <?php outputFooter() ?>
</body>
</html>
