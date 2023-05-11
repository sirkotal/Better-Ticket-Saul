<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');

  //? maybe change throws to something else
  class Department {
    private int $id;
    private string $name;
    private array $agents;

    public function __construct(int $id) {
      if (!Department::exists($id)) {
        throw new Exception('Department does not exist');
      }

      $this->id = $id;

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Department WHERE id = :id');
      $stmt->bindValue(':id', $id);
      $stmt->execute();
      
      $result = $stmt->fetch();
      $this->name = $result['name'];

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE departmentId = :departmentId');
      $stmt->bindValue(':departmentId', $id);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->agents = array_map(function ($row) {
        return new Agent($row['agentId']);
      }, $result);
    }

    /**
     * Check if a department exists (finds a department with the given name)
     * 
     * @param string|int $key The department name if string, department id if int
     * @return bool true if the department exists, false otherwise
     */
    public static function exists(string|int $key): bool {
      $db = getDatabaseConnection();

      if (is_int($key)) {
        $stmt = $db->prepare('SELECT * FROM Department WHERE id = :id');
        $stmt->bindValue(':id', $key);
      } else {
        $stmt = $db->prepare('SELECT * FROM Department WHERE name = :name');
        $stmt->bindValue(':name', $key);
      }

      $stmt->execute();

      $result = $stmt->fetch();
      return $result !== false;
    }

    /**
     * Create a new department
     * 
     * @param string $name The department name
     * @return Department The created department
     */
    public static function create(string $name): Department {
      if (Department::exists($name)) {
        throw new Exception('Department already exists');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Department (name) VALUES (:name)');
      $stmt->bindValue(':name', $name);
      $stmt->execute();

      return new Department((int) $db->lastInsertId());
    }

    /**
     * Delete a department
     * 
     * @param int $id The department id
     * @return array The deleted department info
     */
    public static function delete(int $id): array {
      if (!Department::exists($id)) {
        throw new Exception('Department does not exist');
      }

      $department = new Department($id);

      $info = [
        'id' => $department->getId(),
        'name' => $department->getName(),
        'agents' => $department->getAgents()
      ];

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Department WHERE id = :id');
      $stmt->bindValue(':id', $id);
      $stmt->execute();

      return $info;
    }

    /**
     * Check if an agent is in the department
     * 
     * @param Agent $agent The agent to check
     * @param Department $department The department to check
     * @return bool true if the agent is in the department, false otherwise
     */
    public static function isAgentFromDepartment(Agent $agent, Department $department): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE agentId = :agentId AND departmentId = :departmentId');
      $stmt->bindValue(':agentId', $agent->getId());
      $stmt->bindValue(':departmentId', $department->getId());
      $stmt->execute();

      $result = $stmt->fetch();
      return $result !== false;
    }

    /**
     * Get all departments
     * 
     * @return array All departments
     */
    public static function getAllDepartments(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Department');
      $stmt->execute();

      $result = $stmt->fetchAll();
      return array_map(function ($row) {
        return new Department($row['id']);
      }, $result);
    }

    /**
     * Remove an agent from the department
     * 
     * @param Agent $agent The agent to remove
     * @param bool $exec_query If true, the query will be executed (default: true)
     */
    public function removeAgent(Agent $agent, bool $exec_query = true): void {
      if (!Department::isAgentFromDepartment($agent, $this)) {
        throw new Exception('Agent not in department');
      }

      if ($exec_query) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE agentId = :agentId AND departmentId = :departmentId');
        $stmt->bindValue(':agentId', $agent->getId());
        $stmt->bindValue(':departmentId', $this->id);
        $stmt->execute();
      }

      $this->agents = array_filter($this->agents, function ($a) use ($agent) {
        return $a->getId() !== $agent->getId();
      });

      $agent->removeDepartment($this, false);
    }

    /**
     * Add an agent to the department
     * 
     * @param Agent $agent The agent to add
     */
    public function addAgent(Agent $agent): void {
      if (Department::isAgentFromDepartment($agent, $this)) {
        throw new Exception('Agent already in department');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO AgentDepartment (agentId, departmentId) VALUES (:agentId, :departmentId)');
      $stmt->bindValue(':agentId', $agent->getId());
      $stmt->bindValue(':departmentId', $this->id);
      $stmt->execute();

      $this->agents[] = $agent;
    }

    /**
     * Get the department id
     * 
     * @return int The department id
     */
    public function getId(): int {
      return $this->id;
    }

    /**
     * Get the department name
     * 
     * @return string The department name
     */
    public function getName(): string {
      return $this->name;
    }

    /**
     * Get the department agents
     * 
     * @return array The department agents
     */
    public function getAgents(): array {
      return $this->agents;
    }
  }
?>
