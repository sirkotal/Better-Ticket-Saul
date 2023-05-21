<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead(
  $stylesheets = [
    '/style/create_ticket.css'
  ]
  ) ?>
<body>
  <?php outputHeader() ?>
  <section id="open">
        <h1>Open a Ticket</h1>
        <form type="Submit">
          <label id="title">
            <input type="text" name="title" placeholder="Add your title">
          </label>
          <label id="department">
            <select name="department">
              <option value="none" disabled selected hidden>--Select Deparment--</option>
              <option value="sales">Sales</option>
              <option value="support">Support</option> 
              <option value="billing">Billing</option> 
            </select>
          </label>
          <textarea id="hashtag" name="hashtag" placeholder="Add your hashtags"></textarea>
          <div class="hash-container"></div>
          <button formaction="#" formmethod="post">Open</button>
        </form>
      </section>
  <?php outputFooter() ?>
</body>
</html>
