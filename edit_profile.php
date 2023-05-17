<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');

  $session = new Session();

  if (!$session->isLoggedIn()) {
    header('Location: login.php');
    die();
  }

  require_once(__DIR__ . '/database/user.php');
  
  $client = new Client($session->getUser());
  
  require_once(__DIR__ . '/database/ticket.php');
  
  $username = $client->getUsername();
  $name = $client->getName();
  $email = $client->getEmail();

  // TODO: format html to display ticket info
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <div id="edit-profile">
    <h1>Edit Profile</h1>
    <div id="profile-values">
        <div class="header-value">
            <label class="title">Username</label>
            <button id="Username">Edit</button>    
        </div>
        <p class="value"><?=$username?></p>
        <div class="header-value">
            <label class="title">Name</label>
            <button id="Name">Edit</button>  
        </div>  
        <p class="value"><?=$name?></p>
        <div class="header-value">
            <label class="title">Email</label>
            <button id="Email">Edit</button> 
        </div>   
        <p class="value"><?=$email?></p>
    </div>
    <div id="username-change" class="profile-change">
        <p class="title">Previous Username</p>
        <p class="value"><?=$username?></p>
        <label class="title">Enter the new Username</label>
        <input type="text" name="username"> 
    </div>
    <div id="name-change" class="profile-change">
        <p class="title">Previous Name</p>
        <p class="value"><?=$name?></p>
        <label class="title">Enter the new Name</label>
        <input type="text" name="name"> 
    </div>
    <div id="email-change" class="profile-change">
        <p class="title">Previous Email</p>
        <p class="value"><?=$email?></p>
        <label class="title">Enter the new Email</label>
        <input type="email" name="email"> 
    </div>
  </div>
</body>
<?php outputFooter() ?>