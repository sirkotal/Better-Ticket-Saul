<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');

  //? maybe change throws to something else
  class Department {
    private int $id;
    private string $name;
    private array $agentsIds;

    public function __construct(int $id) {
      if (!Department::exists($id)) {
        throw new Exception('Department does not exist');
      }

      $this->id = $id;

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Department WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      
      $result = $stmt->fetch();
      $this->name = $result['name'];

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE departmentId = :departmentId');
      $stmt->bindValue(':departmentId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->agentsIds = array_map(function ($row) {
        return (int) $row['agentId'];
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
        $stmt->bindValue(':id', $key, PDO::PARAM_INT);
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
     * @return array The deleted department info ready to be json encoded
     */
    public static function delete(int $id): array {
      if (!Department::exists($id)) {
        throw new Exception('Department does not exist');
      }

      $department = new Department($id);
      $info = $department->parseJsonInfo();
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Department WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE departmentId = :departmentId');
      $stmt->bindValue(':departmentId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('UPDATE Ticket SET departmentId = NULL WHERE departmentId = :departmentId');
      $stmt->bindValue(':departmentId', $id, PDO::PARAM_INT);
      $stmt->execute();
      //? should we also set the agent as null in the ticket?

      $stmt = $db->prepare('UPDATE TicketReply SET departmentId = NULL WHERE departmentId = :departmentId');
      $stmt->bindValue(':departmentId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('UPDATE TicketLog SET departmentId = NULL WHERE departmentId = :departmentId');
      $stmt->bindValue(':departmentId', $id, PDO::PARAM_INT);
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
      $stmt->bindValue(':agentId', $agent->getId(), PDO::PARAM_INT);
      $stmt->bindValue(':departmentId', $department->getId(), PDO::PARAM_INT);
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
     * Parse a department info to an array ready to be json encoded
     * 
     * @return array The parsed department info
     */
    public function parseJsonInfo(): array {
      return [
        'id' => $this->getId(),
        'name' => $this->getName(),
        'agents' => array_map(function ($agent) {
          return [
            'username' => $agent->getUsername(),
            'name' => $agent->getName(),
            'email' => $agent->getEmail()
          ];
        }, $this->getAgents())
      ];
    }

    /**
     * Remove an agent from the department
     * 
     * @param Agent $agent The agent to remove
     */
    public function removeAgent(Agent $agent): void {
      if (!Department::isAgentFromDepartment($agent, $this)) {
        throw new Exception('Agent not in department');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE agentId = :agentId AND departmentId = :departmentId');
      $stmt->bindValue(':agentId', $agent->getId(), PDO::PARAM_INT);
      $stmt->bindValue(':departmentId', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->agentsIds = array_filter($this->agentsIds, function ($id) use ($agent) {
        return $id !== $agent->getId();
      });
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
      $stmt->bindValue(':agentId', $agent->getId(), PDO::PARAM_INT);
      $stmt->bindValue(':departmentId', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->agentsIds[] = $agent->getId();
    }

    /**
     * Update the department name
     * 
     * @param string $newName The new department name
     */
    public function update(string $newName): void {
      if (Department::exists($newName)) {
        throw new Exception('Department already exists');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Department SET name = :name WHERE id = :id');
      $stmt->bindValue(':name', $newName);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->name = $newName;
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
      return array_map(function ($id) {
        return new Agent($id);
      }, $this->agentsIds);
    }
  }
?>
