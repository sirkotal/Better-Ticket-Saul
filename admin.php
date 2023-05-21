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
        <button class='update_roles' >Update Roles</button>
        <button class='add_department' >Add New Department</button>
        <button class='add_status'>Add New Status</button>
        <button class='agent_assign' >Assign Agents</button>
        <button class='agent_dismiss' >Dismiss Agents</button>
    </div>
    <div id="update-role" class="admin-forms update_roles">
        <?php foreach($users as $user) {
                if ($user->getId()!==$session->getUser()->getId()){?>
                <p class='title' id='<?=$user->getUsername()?>' ><?=$user->getUsername()?> - <?php if($user->isAdmin($user->getId())): ?>Admin <?php elseif($user->isAgent($user->getId())): ?>Agent <?php else: ?>Client <?php endif; ?></p>
                <input class='user-id' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->getId()?>>
                <input id='user-agent' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->isAgent($user->getId())?>>
                <input id='user-admin' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->isAdmin($user->getId())?>>
                <select name="Roles" id=<?=$user->getUsername()?>>
                    <option value="none">--Default--</option>
                    <option value="Client" >Client</option>
                    <option value="Agent">Agent</option>
                    <option value="Admin">Admin</option>
                </select>
        <?php }}?>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button" id="enter-role">Enter</button> 
        </div>
    </div>
    <div id="New-Department" class="admin-forms add_department">
        <input id='department-id' type='hidden' name='id' value=<?=count(DEPARTMENT::getAllDepartments())?>>
        <label class="title">Enter the new Department Name</label>
        <input type="text" name="department">
        <p class = "error" id = "error-username"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button">Enter</button> 
        </div>
    </div>
    <div id="Add-Status" class="admin-forms add_status">
        <label class="title">Enter the new Status Name</label>
        <input type="text" name="status">
        <input type="color" name="status-color">
        <p class = "error" id = "error-username"></p>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button" id="enter-role">Enter</button> 
        </div>
    </div>
    <div id="remove-department" class="admin-forms agent_dismiss">
        <?php foreach($users as $user) {
            if ($user->isAgent($user->getId()) && count($user->getDepartments())!=0){?>
                <p class='title' id='<?=$user->getUsername()?>' ><?=$user->getUsername()?> - <?php if($user->isAdmin($user->getId())): ?>Admin <?php elseif($user->isAgent($user->getId())): ?>Agent <?php else: ?>Client <?php endif; ?></p>
                <input class='user-id' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->getId()?>>
                <select name="Departments" id=<?=$user->getUsername()?>>
                    <option name=<?=$user->getUsername()?> value="none">--Default--</option>
                    <?php foreach($user->getDepartments() as $department) {?>
                        <option value=<?=$department->getId()?>><?=$department->getName()?></option>
                    <?php } ?>
                </select>
        <?php }}?>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button agent_dismiss" id="enter-role">Enter</button> 
        </div>
    </div>
    <div id="add-department" class="admin-forms agent_assign">
        <?php foreach($users as $user) {
                if ($user->isAgent($user->getId()) ){?>
                    <p class='title' id='<?=$user->getUsername()?>' ><?=$user->getUsername()?> - <?php if($user->isAdmin($user->getId())): ?>Admin <?php elseif($user->isAgent($user->getId())): ?>Agent <?php else: ?>Client <?php endif; ?></p>
                    <input class='user-id' type='hidden' name=<?=$user->getUsername()?> value=<?=$user->getId()?>>
                    <select name="Departments" id=<?=$user->getUsername()?>>
                        <option name=<?=$user->getUsername()?> value="none">--Default--</option>
                        <?php $departments = DEPARTMENT::getAllDepartments(); foreach($departments as $department) { 
                                if (!DEPARTMENT::isAgentFromDepartment($user, $department)){?>
                                    <option value=<?=$department->getId()?>><?=$department->getName()?></option>
                        <?php }}?>
                    </select>
        <?php }}?>
        <div class="change-buttons">
          <button class="back-button">Back</button>
          <button class="enter-button" id="enter-role">Enter</button> 
        </div>
    </div>
  </div>
  <?php outputFooter() ?>
</body>
</html>