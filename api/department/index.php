<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../../lib/http_status.php');
  require_once(__DIR__ . '/../../lib/api.php');
  require_once(__DIR__ . '/../../database/department.php');

  switch ($_SERVER['REQUEST_METHOD']) {
    case RequestMethod::GET:
      $departments = Department::getAllDepartments();
      $body = [];

      foreach ($departments as $department) {
        $agent_names = [];
        foreach ($department->getAgents() as $agent) {
          $agent_names[] = $agent->getUsername();
        }

        $body[] = [
          'name' => $department->getName(),
          'agents' => $agent_names
        ];
      }

      API::sendGetResponse(HttpStatus::OK, $body);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
