<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <h1>Departments</h1>
  <table class="department-table">
    <thead>
      <tr>
        <th>Department</th>
        <th>Assigned Personnel</th>
        <th>Assigned Tickets</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Technical Support</td>
        <td>John Doe, Jane Smith</td>
        <td>Ticket #001, Ticket #004</td>
      </tr>
      <tr>
        <td>Customer Service</td>
        <td>Mark Johnson, Sarah Lee</td>
        <td>Ticket #002, Ticket #003</td>
      </tr>
      <tr>
        <td>Accounting</td>
        <td>Robert Kim</td>
        <td>Ticket #005</td>
      </tr>
    </tbody>
  </table> 
  <?php outputFooter() ?>
</body>
</html>