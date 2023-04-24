<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');

  //? maybe change throws to something else
  class Department {
    private string $name;
    private array $agents;

    public function __construct(string $name) {
      if (!Department::exists($name)) {
        throw new Exception('Department does not exist');
      }

      $this->name = $name;

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE department = :department');
      $stmt->bindValue(':department', $name);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->agents = array_map(function ($row) {
        return new Agent($row['agent']['username']);
      }, $result);
    }

    /**
     * Check if a department exists (finds a department with the given name)
     * 
     * @param string $name The department name
     * 
     * @return bool true if the department exists, false otherwise
     */
    public static function exists(string $name): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Department WHERE name = :name');
      $stmt->bindValue(':name', $name);
      $stmt->execute();

      $result = $stmt->fetch();
      return $result !== false;
    }

    /**
     * Create a new department
     * 
     * @param string $name The department name
     */
    public static function create(string $name): void {
      if (Department::exists($name)) {
        throw new Exception('Department already exists');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Department (name) VALUES (:name)');
      $stmt->bindValue(':name', $name);
      $stmt->execute();
    }

    /**
     * Delete a department
     * 
     * @param string $name The department name
     */
    public static function delete(string $name): void {
      if (!Department::exists($name)) {
        throw new Exception('Department does not exist');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Department WHERE name = :name');
      $stmt->bindValue(':name', $name);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE department = :department');
      $stmt->bindValue(':department', $name);
      $stmt->execute();
    }

    /**
     * Check if an agent is in the department
     * 
     * @param Agent $agent The agent to check
     * @param Department $department The department to check
     * 
     * @return bool true if the agent is in the department, false otherwise
     */
    public static function isAgentFromDepartment(Agent $agent, Department $department): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE agent = :agent AND department = :department');
      $stmt->bindValue(':agent', $agent->getUsername());
      $stmt->bindValue(':department', $department->getName());
      $stmt->execute();

      $result = $stmt->fetch();
      return $result !== false;
    }

    /**
     * Remove an agent from the department
     * 
     * @param Agent $agent The agent to remove
     * @param bool $exec_query If true, the query will be executed (default: true)
     */
    public function removeAgent(Agent $agent, bool $exec_query = true): void {
      if (!in_array($agent, $this->agents)) {
        throw new Exception('Agent not in department');
      }

      if ($exec_query) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE agent = :agent AND department = :department');
        $stmt->bindValue(':agent', $agent->getUsername());
        $stmt->bindValue(':department', $this->name);
        $stmt->execute();
      }

      $this->agents = array_filter($this->agents, function ($a) use ($agent) {
        return $a->getUsername() !== $agent->getUsername();
      });

      $agent->removeDepartment($this, false);
    }

    /**
     * Add an agent to the department
     * 
     * @param Agent $agent The agent to add
     */
    public function addAgent(Agent $agent): void {
      if (in_array($agent, $this->agents)) {
        throw new Exception('Agent already in department');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO AgentDepartment (agent, department) VALUES (:agent, :department)');
      $stmt->bindValue(':agent', $agent->getUsername());
      $stmt->bindValue(':department', $this->name);
      $stmt->execute();

      $this->agents[] = $agent;
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
