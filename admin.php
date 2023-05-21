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

  $users = USER::getAllUsers();
?>

<?php outputHead() ?>
<body>
  <?php outputHeader() ?>
  <div id="menu-admin">
    <div id="admin-options">
        <h1>Dashboard</h1>  
        <button >Update Roles</button>
        <button >Add New Department</button>
        <button >Add New Status</button>
        <button >Assign Agents</button>
    </div>
   <!-- <div id="update-role" class="admin-forms">
        <?php foreach($users as $user) {
                if ($user->getId()!==$session->getUser()->getId()){?>
                <p class='title' id=<?=$user->getUsername()?> ><?=$user->getUsername()?> - <?php if($user->isAdmin($user->getId())): ?>Admin <?php elseif($user->isAgent($user->getId())): ?>Agent <?php else: ?>Client <?php endif; ?></p>
                <input id='user-id' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->getId()?>>
                <input id='user-agent' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->isAgent($user->getId())?>>
                <input id='user-admin' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->isAdmin($user->getId())?>>
                <select name="Roles" id=<?=$user->getUsername()?>>
                    <option name=<?=$user->getUsername()?> value="none" disabled selected hidden>--Default--</input>
                    <option name=<?=$user->getUsername()?> value="Client" >Client</input>
                    <option name=<?=$user->getUsername()?> value="Agent">Agent</input>
                    <option name=<?=$user->getUsername()?> value="Admin">Admin</input>
                </select>
        <?php }}?>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button" id="enter-role">Enter</button> 
        </div>
    </div> -->
    <!--<div id="Add-Department" class="admin-forms">
        <label class="title">Enter the new Department Name</label>
        <input type="text" name="department">
        <input type="color" name="department-color">
        <p class = "error" id = "error-username"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button">Enter</button> 
        </div>
    </div>-->
    <div id="Add-Status" class="admin-forms">
        <label class="title">Enter the new Status Name</label>
        <input type="text" name="status">
        <p class = "error" id = "error-username"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button" id="enter-role">Enter</button> 
        </div>
    </div>
  </div>
  <?php outputFooter() ?>
</body>
</html>