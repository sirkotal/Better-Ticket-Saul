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
    case RequestMethod::POST:
      $json_data = file_get_contents('php://input');
      $data = json_decode($json_data, true);

      if (!array_key_exists('name', $data)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Missing required field');
        return;
      }

      if (count($data) > 1) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Too many fields');
        return;
      }

      $name = $data['name'];

      if (Department::exists($name)) {
        API::sendError(HttpStatus::BAD_REQUEST, 'Department already exists');
        return;
      }

      $department = Department::create($name);
      $body = [
        'name' => $department->getName()
      ];

      API::sendPostResponse(HttpStatus::CREATED, $body);
      return;
    default:
      API::sendError(HttpStatus::METHOD_NOT_ALLOWED, 'Method not allowed');
      return;
  }
?>
