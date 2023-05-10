<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/department.php');

  abstract class User {
    //? maybe private?
    protected int $id;
    protected string $username;
    protected string $name;
    protected string $email;

    public function __construct(int $id) {
      $this->id = $id;

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User WHERE id = :id');
      $stmt->bindValue(':id', $id);
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
     * @param string $username The user username
     * @return bool true if the user exists, false otherwise
     */
    public static function exists(string $username): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User WHERE username = :username');
      $stmt->bindValue(':username', $username);
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
     * @return bool true if the user is valid, false otherwise
     */
    public static function isValid(string $username, string $password): bool {
      $db = getDatabaseConnection();
      
      $stmt = $db->prepare('SELECT * FROM User WHERE username = :username AND password = :password');
      $stmt->bindValue(':username', $username);

      $sha1_password = sha1($password);

      $stmt->bindValue(':password', $sha1_password);
      $stmt->execute();

      $result = $stmt->fetch();
      
      return $result !== false;
    }

    /**
     * Create a new user in the database
     * A new user is created as a client by default
     * 
     * @param string $username The user username
     * @param string $name The user name
     * @param string $email The user email
     * @param string $password The user password
     */
    public static function create(string $username, string $name, string $email, string $password): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO User (username, name, email, password) VALUES (:username, :name, :email, :password)');
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':email', $email);

      $sha1_password = sha1($password);

      $stmt->bindValue(':password', $sha1_password);
      $stmt->execute();

      $stmt = $db->prepare('INSERT INTO Client (username) VALUES (:username)');
      $stmt->bindValue(':username', $username);
      $stmt->execute();
    }

    /**
     * Get all users from the database
     * 
     * @return array Array of users
     */
    public static function getAllUsers(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User');
      $stmt->execute();

      $result = $stmt->fetchAll();

      return array_map(function($row) {
        if (User::isAgent($row['username'])) {
          return new Agent($row['username']);
        } else if (User::isAdmin($row['username'])) {
          return new Admin($row['username']);
        } else {
          return new Client($row['username']);
        }
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
      $stmt->bindValue(':id', $id);
      $stmt->execute();

      $result = $stmt->fetch();

      if ($result === false) {
        throw new Exception('User not found');
      }

      if (User::isAdmin($result['username'])) {
        return new Admin($result['username']);
      } else if (User::isAgent($result['username'])) {
        return new Agent($result['username']);
      } else {
        return new Client($result['username']);
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
      $stmt->bindValue(':userId', $userId);
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

      $stmt = $db->prepare('SELECT * FROM Admin WHERE userId = :userId');
      $stmt->bindValue(':userId', $userId);
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
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
  }

  class Agent extends Client {
    private array $departments;

    public function __construct(int $userId) {
      if (!User::isAgent($userId)) {
        throw new Exception('User is not an agent');
      }
      
      parent::__construct($userId);

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM AgentDepartment WHERE agentId = :agentId');
      $stmt->bindValue(':agentId', $this->id);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->departments = array_map(function ($row) {
        return $row['departmentId'];
      }, $result);
    }

    /**
     * Remove a department from the agent
     * 
     * @param Department $department The department to remove
     * @param bool $exec_query If true, the query will be executed, if not just removes from the class array (default: true)
     */
    public function removeDepartment(Department $department, bool $exec_query = true): void {
      if (!in_array($department, $this->departments)) {
        throw new Exception('Agent is not in the department');
      }
      
      if ($exec_query) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE agent = :agent AND department = :department');
        $stmt->bindValue(':agent', $this->username);
        $stmt->bindValue(':department', $department->getName());
        $stmt->execute();
      }

      $this->departments = array_filter($this->departments, function ($d) use ($department) {
        return $d->getName() !== $department->getName();
      });

      $department->removeAgent($this, false);
    }

    /**
     * Get the agent departments
     * 
     * @return array The agent departments
     */
    public function getDepartments(): array { //! for now its like this, but it should be an array of Department objects
      $departments = [];

      foreach ($this->departments as $department) {
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
  }
?>
