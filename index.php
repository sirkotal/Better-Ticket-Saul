<?php
  declare (strict_types = 1);

  require_once('/templates/common.php');
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <!-- TODO: change what to on the index page -->
  <main>
    <h2>Welcome to our Trouble Ticket Management System</h2>
    <p>
      Our system allows you to submit and track trouble tickets related to technical issues, customer support,
      or any other inquiries. Please use the links above to submit a ticket, view your tickets, check the FAQs,
      or contact us for assistance.
    </p>
    <section>
      <h3><a href="../tickets/create.html">Submit a Ticket</a></h3>
      <form action="/submit-ticket" method="post">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="issue">Issue</label>
        <textarea id="issue" name="issue" required></textarea>
        <button type="submit">Submit</button>
      </form>
    </section>
    <section>
      <h3><a href="../tickets/view.html">View Tickets</a></h3>
      <p>
        If you have already submitted a ticket, you can view the status and details of your ticket by logging
        in to your account or using your ticket reference number.
      </p>
    </section>
    <section>
      <h3><a href="#">FAQs</a></h3>
      <p>
        Check our frequently asked questions for answers to common inquiries. If you can't find the information
        you're looking for, please feel free to contact us for further assistance.
      </p>
    </section>
    <section>
      <h3><a href="#">Contact Us</a></h3>
      <p>
        If you need to reach out to us for any reason, please use the contact information provided below or
        fill out the contact form. We will get back to you as soon as possible.
      </p>
    </section>
  </main>
  <?php outputFooter() ?>
</body>
</html>
