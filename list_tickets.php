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
    <div class="upper-row">
      <h1>My Tickets:</h1>
      <div class="filters"> <!-- TODO: change this to js -->
        <div class="filter">
          <label for="filter-department">Department:</label>
          <select id="filter-department">
            <option value="all">All</option>
          </select>
        </div>
        <div class="filter">
          <label for="filter-status">Status:</label>
          <select id="filter-status">
            <option value="all">All</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</body>
<?php outputFooter() ?>
