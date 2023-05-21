<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead(
  $stylesheets = [
    '/style/create_ticket.css'
  ],
  $scripts = [
    '/script/ticket_hashtag.js'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <section id="open">
    <h1>Open a Ticket</h1>
    <form type="Submit">
      <div class="create-first">
        <label id="title">
          <input type="text" name="title" placeholder="Add your title">
        </label>
        <label id="department">
          <select name="department">
            <option value="default" disabled selected hidden>--Select Deparment--</option>
            <option value="none">None</option>
            <option value="sales">Sales</option>
            <option value="support">Support</option> 
            <option value="billing">Billing</option> 
          </select>
        </label>
      </div>
      <input id="ticket-text" name="hashtag" type="text" placeholder="Tell us what's up!">
      <div class="create-second">
        <div class="hash-selection">
          <select id="hashtag-selector" name="hashtag">
            <option selected hidden>--Select Hashtag--</option>
            <option value="#yes">#yes</option>
            <option value="#no">#no</option>
            <option value="#test">#test</option>
            <option value="#why">#why</option>
          </select>
          <button type="button" id="add-hashtag">Add</button>
        </div>  
        <div id="hashtag-container"></div>
      </div>  
      <button formaction="#" formmethod="post">Open</button>
    </form>
  </section>
  <?php outputFooter() ?>
</body>
</html>