<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/templates/common.php');
  require_once(__DIR__ . '/lib/session.php');
  require_once(__DIR__ . '/database/department.php');
  require_once(__DIR__ . '/database/user.php');

  $session = new Session();

  if (!$session->isLoggedIn()) {
    header('Location: login.php');
    die();
  }
  
  $departs = Department::getAllDepartments();
?>

<?php function outputDepartment(Department $department) {
  $department_id = $department->getId();
  $department_name = $department->getName();
  $department_agents = $department->getAgents() ?? []; ?>

  <div class="department" data-id="<?= $department_id ?>">
    <div class="agent-section">
      <div class="agent-overall">
        <h2 class="title"><a href="#"><?= $department_name ?></a></h2>
        <p class="agent-count">(<?= count($department_agents) ?>)</p>
      </div>   
      <?php outputDropdownButton() ?>
    </div>  
    <div class="agent-section"> 
      <div class="agents-info">
        <?php foreach ($department_agents as $agent) {
          outputAgent($agent);
        } ?>
      </div>
    </div>
  </div>
<?php } ?>

<?php function outputAgent(Agent $agent) {
  // $agent_id = $agent->getId();
  $agent_name = $agent->getName();
  $agent_username = $agent->getUsername();
  $agent_email = $agent->getEmail(); ?>
  
  <div class="agent-data">
    <p class="agent-name"><?= $agent_name ?></p>
    <p class="agent-username"><?= $agent_username ?></p>
    <p class="agent-email"><?= $agent_email ?></p>
  </div>  
<?php } ?>

<?php outputHead(
  $stylesheets = [
    '/style/departments.css'
  ],
  $scripts = [
    '/script/departments.js'
  ]
) ?>
<body>
  <?php outputHeader() ?>
  <div id="departments">
    <h1>Departments:</h1>
    <?php
      foreach ($departs as $depart) { 
        outputDepartment($depart);
      }
    ?>
  </div>
</body>
<?php outputFooter() ?>