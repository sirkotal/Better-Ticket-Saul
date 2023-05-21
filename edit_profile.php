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
  
  $user = $session->getUser();
  
  require_once(__DIR__ . '/database/ticket.php');
  
  $username = $user->getUsername();
  $name = $user->getName();
  $email = $user->getEmail();
  $id = $user->getId();

  // TODO: format html to display ticket info
?>

<?php outputHead(
  $stylesheets = [
    '/style/edit_profile.css'
  ],
  $scripts = [
    '/scripts/edit_profile.js'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <div id="edit-profile">
    <div id="backgroud-popUp">
      <h1>Edit Profile</h1>
      <div id="profile-values">
          <input type=hidden name=id value=<?=$id?>>
          <div class="header-value">
              <label class="title">Username</label>
              <button class="username-button">Edit</button>    
          </div>
          <p class="value value-username"><?=$username?></p>
          <div class="header-value">
              <label class="title">Name</label>
              <button class="name-button">Edit</button>  
          </div>  
          <p class="value value-name"><?=$name?></p>
          <div class="header-value">
              <label class="title">Email</label>
              <button class="email-button">Edit</button> 
          </div>   
          <p class="value value-email"><?=$email?></p>
          <button class="password-button">Change Password</button>
      </div>
    </div>
    <div id="username-change" class="profile-change">
        <p class="title">Previous Username</p>
        <p class="value value-username"><?=$username?></p>
        <label class="title">Enter the new Username</label>
        <input type="text" name="username"> 
        <p class = "error" id = "error-username"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="username-button" id="save-username">Save</button> 
        </div>
    </div>
    <div id="name-change" class="profile-change">
        <p class="title">Previous Name</p>
        <p class="value value-name"><?=$name?></p>
        <label class="title">Enter the new Name</label>
        <input type="text" name="name">
        <p class = "error" id = "error-name"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="name-button" id="save-name">Save</button>  
        </div>
    </div>
    <div id="email-change" class="profile-change">
        <p class="title">Previous Email</p>
        <p class="value value-email"><?=$email?></p>
        <label class="title">Enter the new Email</label>
        <input type="email" name="email">
        <p class = "error" id = "error-email"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="email-button" id="save-email">Save</button>  
        </div>
    </div>
    <div id="password-change" class="profile-change">
        <p class="title">Enter the new Password</p>
        <input type="password" name="password">
        <label class="title">Confirm Password</label>
        <input type="password" name="confirm-password">
        <p class = "error" id = "error-password"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="password-button" id="save-password">Save</button>  
        </div>
    </div>
  </div>
</body>
<?php outputFooter() ?>