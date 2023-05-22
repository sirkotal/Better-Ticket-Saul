<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');

  require_once(__DIR__ . '/lib/session.php');
  $session = new Session();

  $user = $session->getUser();

  require_once(__DIR__ . '/database/department.php');
  require_once(__DIR__ . '/database/hashtag.php');

  $departments = Department::getAllDepartments();
  $hashtags = new Hashtag();
  $hashtags = $hashtags->getHashtags();
?>

<?php outputHead(
  $stylesheets = [
    '/style/create_ticket.css'
  ],
  $scripts = [
    '/script/ticket_hashtag.js',
    '/script/create_ticket.js'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <section id="open">
    <h1>Open a Ticket</h1>
      <div class="create-first">
        <label id="title">
          <input type="text" name="title" placeholder="Add your title">
        </label>
        <label id="department">
          <select name="department">
            <option value="none" disabled selected hidden>--Select Deparment--</option>
            <option value="none">None</option>
            <?php foreach ($departments as $department) { ?>
              <option value="<?= $department->getId() ?>"><?= $department->getName() ?></option>
            <?php } ?>
          </select>
        </label>
      </div>
      <textarea id="ticket-text" name="text" placeholder="Tell us what's up!"></textarea>
      <div class="create-second">
        <div class="hash-selection">
          <select id="hashtag-selector" name="hashtag">
            <option selected hidden>--Select Hashtag--</option>
            <?php foreach ($hashtags as $hashtag) { ?>
              <option value="<?= $hashtag ?>"><?= $hashtag ?></option>
            <?php } ?>
          </select>
          <button type="button" id="add-hashtag">Add</button>
        </div>  
        <div id="hashtag-container"></div>
      </div>  
      <button id="open-button">Open</button>
      <input id="client-id" type="hidden" name="client_id" value="<?= $user->getId() ?>">
  </section>
  <?php outputFooter() ?>
</body>
</html>