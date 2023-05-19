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
  //var_dump($departs);
?>

<?php function outputDepartment(Department $department) {
  $department_id = $department->getId();
  $department_name = $department->getName();
  $department_agents = $department->getAgents() === null ? 'No agents' : $department->getAgents(); ?>

  <div class="department" data-id="<?= $department_id ?>">
    <h2 class="title"><a href="#"><?= $department_name ?></a></h2>
    <div class="agent-section">
      <p class="agent-count"><?= count($department_agents) ?></p>
      <button class="agent-button">
				<span class="proto-button"><i class='far fa-caret-square-down'></i></span>
			</button>
    </div>
  </div>
<?php } ?>

<?php outputHead(
  $stylesheets = [
    '/style/departments.css'
  ],
  $scripts = [
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