<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/department.php');

  abstract class User {
    protected int $id;
    protected string $username;
    protected string $name;
    protected string $email;

    public function __construct(int $id) {
      $this->id = $id;

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      if ($result === false) {
        throw new Exception('User not found');
      }

      $this->username = $result['username'];
      $this->name = $result['name'];
      $this->email = $result['email'];
    }

    /**
     * Check if a user exists (finds a user with the given username)
     * 
     * @param string|int $key The username or id
     * @return bool true if the user exists, false otherwise
     */
    public static function exists(string|int $key): bool {
      $db = getDatabaseConnection();

      if (is_int($key)) {
        $stmt = $db->prepare('SELECT id FROM User WHERE id = :id');
        $stmt->bindValue(':id', $key, PDO::PARAM_INT);
      } else {
        $stmt = $db->prepare('SELECT id FROM User WHERE username = :username');
        $stmt->bindValue(':username', $key);
      }
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
    }

    /**
     * Check if an email is already in use
     * 
     * @param string $email The email
     * @return bool true if the email is already in use, false otherwise
     */
    public static function emailExists(string $email): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User WHERE email = :email');
      $stmt->bindValue(':email', $email);
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
    }
    
    /**
     * Check if a user is valid
     * 
     * @param string $username The user username
     * @param string $password The user password
     * 
     * @return int|bool The user id if the user is valid, false otherwise
     */
    public static function isValid(string $username, string $password): int|bool {
      $db = getDatabaseConnection();
      
      $stmt = $db->prepare('SELECT id FROM User WHERE username = :username AND password = :password');
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':password', sha1($password));
      $stmt->execute();

      $result = $stmt->fetch();

      if ($result === false) {
        return false;
      }
      
      return (int) $result['id'];
    }

    /**
     * Create a new user in the database
     * A new user is created as a client by default
     * 
     * @param string $username The user username
     * @param string $name The user name
     * @param string $email The user email
     * @param string $password The user password
     * @return User The created user
     */
    public static function create(string $username, string $name, string $email, string $password): User {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO User (username, name, email, password) VALUES (:username, :name, :email, :password)');
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':email', $email);

      $sha1_password = sha1($password);

      $stmt->bindValue(':password', $sha1_password);
      $stmt->execute();

      $userId = (int) $db->lastInsertId();

      $stmt = $db->prepare('INSERT INTO Client (userId) VALUES (:userId)');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      return User::getUserById($userId);
    }

    /**
     * Delete a user from the database
     * 
     * @param int $userId The user id
     * @return array The deleted user info
     */
    public static function delete(int $userId): array {
      if (!User::exists($userId)) {
        throw new Exception('User not found');
      }

      $info = User::getUserById($userId)->parseJsonInfo();
      
      $db = getDatabaseConnection();

      if (User::isAdmin($userId)) {
        $stmt = $db->prepare('DELETE FROM Admin WHERE userId = :userId');
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
      }

      if (User::isAgent($userId)) {
        $stmt = $db->prepare('DELETE FROM Agent WHERE userId = :userId');
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE agentId = :userId');
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('UPDATE Ticket SET agentId = NULL WHERE agentId = :userId');
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('UPDATE TicketReply SET agentId = NULL WHERE agentId = :userId');
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('UPDATE TicketLog SET agentId = NULL WHERE agentId = :userId');
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
      }

      $stmt = $db->prepare('DELETE FROM Client WHERE userId = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('UPDATE Ticket SET clientId = NULL WHERE clientId = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM User WHERE id = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      return $info;
    }

    /**
     * Get all users from the database
     * 
     * @return array Array of users
     */
    public static function getAllUsers(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT id FROM User');
      $stmt->execute();

      $result = $stmt->fetchAll();

      return array_map(function($row) {
        return User::getUserById((int) $row['id']);
      }, $result);
    }

    /**
     * Get user by id
     * 
     * @param int $id The user id
     * @return User The user
     */
    public static function getUserById(int $id): User {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      if ($result === false) {
        throw new Exception('User not found');
      }

      if (User::isAdmin($result['id'])) {
        return new Admin($result['id']);
      } else if (User::isAgent($result['id'])) {
        return new Agent($result['id']);
      } else {
        return new Client($result['id']);
      }
    }

    /**
     * Check if the user is an agent
     * 
     * @param int $userId The user id
     * @return bool true if the user is an agent, false otherwise
     */
    public static function isAgent(int $userId): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Agent WHERE userId = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
    }

    /**
     * Check if the user is an admin
     * 
     * @param int $userId The user id
     * @return bool true if the user is an admin, false otherwise
     */
    public static function isAdmin(int $userId): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT userId FROM Admin WHERE userId = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
    }

    /**
     * Make a user an agent
     * 
     * @param int $userId The user id
     * @return Agent The user (now an agent)
     */
    public static function makeAgent(int $userId): Agent {
      if (User::isAgent($userId)) {
        throw new Exception('User is already an agent');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Agent (userId) VALUES (:userId)');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      return new Agent($userId);
    }

    /**
     * Make a user an admin
     * 
     * @param int $userId The user id
     * @return Admin The user (now an admin)
     */
    public static function makeAdmin(int $userId): Admin {
      if (User::isAdmin($userId)) {
        throw new Exception('User is already an admin');
      }

      if (!User::isAgent($userId)) {
        User::makeAgent($userId);
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Admin (userId) VALUES (:userId)');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      return new Admin($userId);
    }

    /**
     * Demote an agent to a client
     * 
     * @param int $userId The user id
     * @return Client The user (now a client)
     */
    public static function demoteAgent(int $userId): Client {
      if (User::isAdmin($userId)) {
        User::demoteAdmin($userId);
        //throw new Exception('User is an admin');
      }
      
      if (!User::isAgent($userId)) {
        throw new Exception('User is not an agent');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Agent WHERE userId = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      return new Client($userId);
    }

    /**
     * Demote an admin to an agent
     * 
     * @param int $userId The user id
     * @return Agent The user (now an agent)
     */
    public static function demoteAdmin(int $userId): Agent {
      if (!User::isAdmin($userId)) {
        throw new Exception('User is not an admin');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Admin WHERE userId = :userId');
      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->execute();

      return new Agent($userId);
    }

    /**
     * Parse a user info to an array ready to be json encoded
     * 
     * @return array The parsed user info
     */
    abstract public function parseJsonInfo(): array;

    /**
     * Update the user name
     * 
     * @param string $name The new user name
     */
    public function setUsername(string $username): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE User SET username = :username WHERE id = :id');
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->username = $username;
    }

    /**
     * Update the user name
     * 
     * @param string $name The new user name
     */
    public function setName(string $name): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE User SET name = :name WHERE id = :id');
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->name = $name;
    }

    /**
     * Update the user email
     * 
     * @param string $email The new user email
     */
    public function setEmail(string $email): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE User SET email = :email WHERE id = :id');
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->email = $email;
    }

    /**
     * Update the user password
     * 
     * @param string $password The new user password
     */
    public function setPassword(string $password): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE User SET password = :password WHERE id = :id');
      $stmt->bindValue(':password', sha1($password));
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();
    }

    /**
     * Get the user id
     * 
     * @return int User id
     */
    public function getId(): int {
      return $this->id;
    }

    /**
     * Get the user id from the database
     * 
     * @return string User Username
     */
    public function getUsername(): string {
      return $this->username;
    }

    /**
     * Get the user name from the database
     * 
     * @return string User name
     */
    public function getName(): string {
      return $this->name;
    }

    /**
     * Get the user email from the database
     * 
     * @return string User email
     */
    public function getEmail(): string {
      return $this->email;
    }
  }

  class Client extends User {
    public function __construct(int $userId) {
      parent::__construct($userId);
    }

    public function parseJsonInfo(): array {
      return [
        'id' => $this->id,
        'username' => $this->username,
        'name' => $this->name,
        'email' => $this->email,
        'role' => 'client',
      ];
    }
  }

  class Agent extends Client {
    protected array $departmentsIds;

    public function __construct(int $userId) {
      if (!User::isAgent($userId)) {
        throw new Exception('User is not an agent');
      }
      
      parent::__construct($userId);

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE agentId = :agentId');
      $stmt->bindValue(':agentId', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->departmentsIds = array_map(function ($row) {
        return $row['departmentId'];
      }, $result);
    }

    public function parseJsonInfo(): array {
      $departmentNames = array_map(function ($departmentId) {
        $department = new Department($departmentId);
        return $department->getName();
      }, $this->departmentsIds);

      return [
        'id' => $this->id,
        'username' => $this->username,
        'name' => $this->name,
        'email' => $this->email,
        'role' => 'agent',
        'departments' => $departmentNames,
      ];
    }

    /**
     * Remove a department from the agent
     * 
     * @param Department $department The department to remove
     */
    public function removeDepartment(Department $department): void {
      if (!in_array($department->getId(), $this->departmentsIds)) {
        throw new Exception('Agent is not in the department');
      }
      
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE agentId = :agentId AND departmentId = :departmentId');
      $stmt->bindValue(':agentId', $this->id, PDO::PARAM_INT);
      $stmt->bindValue(':department', $department->getId(), PDO::PARAM_INT);
      $stmt->execute();

      $this->departmentsIds = array_filter($this->departmentsIds, function ($id) use ($department) {
        return $id !== $department->getId();
      });
    }

    /**
     * Get the agent departments
     * 
     * @return array The agent departments
     */
    public function getDepartments(): array {
      $departments = [];

      foreach ($this->departmentsIds as $department) {
        $departments[] = new Department($department);
      }

      return $departments;
    }
  }

  class Admin extends Agent {
    public function __construct(int $userId) {
      if (!User::isAdmin($userId)) {
        throw new Exception('User is not an admin');
      }

      parent::__construct($userId);
    }

    public function parseJsonInfo(): array {
      $departmentNames = array_map(function ($departmentId) {
        $department = new Department($departmentId);
        return $department->getName();
      }, $this->departmentsIds);

      return [
        'id' => $this->id,
        'username' => $this->username,
        'name' => $this->name,
        'email' => $this->email,
        'role' => 'admin',
        'departments' => $departmentNames,
      ];
    }
  }
?>
