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
  
  $user = User::getUserById($id);
  
  require_once(__DIR__ . '/database/ticket.php');
  
  $username = $user->getUsername();
  $name = $user->getName();
  $email = $user->getEmail();

  // TODO: format html to display ticket info
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <div id="edit-profile">
    <div id="backgroud-popUp">
      <h1>Edit Profile</h1>
      <div id="profile-values">
          <div class="header-value">
              <label class="title">Username</label>
              <button class="username-button">Edit</button>    
          </div>
          <p class="value"><?=$username?></p>
          <div class="header-value">
              <label class="title">Name</label>
              <button class="name-button">Edit</button>  
          </div>  
          <p class="value"><?=$name?></p>
          <div class="header-value">
              <label class="title">Email</label>
              <button class="email-button">Edit</button> 
          </div>   
          <p class="value"><?=$email?></p>
      </div>
    </div>
    <div id="username-change" class="profile-change">
        <p class="title">Previous Username</p>
        <p class="value"><?=$username?></p>
        <label class="title">Enter the new Username</label>
        <input type="text" name="username"> 
        <button class="username-button">Save</button> 
    </div>
    <div id="name-change" class="profile-change">
        <p class="title">Previous Name</p>
        <p class="value"><?=$name?></p>
        <label class="title">Enter the new Name</label>
        <input type="text" name="name">
        <button class="name-button">Save</button>  
    </div>
    <div id="email-change" class="profile-change">
        <p class="title">Previous Email</p>
        <p class="value"><?=$email?></p>
        <label class="title">Enter the new Email</label>
        <input type="email" name="email">
        <button class="email-button">Save</button>  
    </div>
  </div>
</body>
<?php outputFooter() ?>