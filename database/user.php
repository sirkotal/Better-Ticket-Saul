<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');

  abstract class User {
    //? maybe private?
    protected string $username;
    protected string $name;
    protected string $email;

    public function __construct(string $username, string $password) {
      if (!User::isValid($username, $password)) {
        throw new Exception('User does not exist');
      }

      $this->username = $username;

      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM User WHERE username = :username');
      $stmt->bindValue(':username', $username);
      $stmt->execute();

      $result = $stmt->fetch();

      $this->name = $result['name'];
      $this->email = $result['email'];
    }

    /**
     * Check if a user exists (finds a user with the given username)
     * 
     * @param string $username The user username
     * 
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
     * 
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
    public static function create(string $username, string $name, string $email, string $password): bool {
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
      $result = $stmt->execute();

      return $result;
    }

    /**
     * Check if the user is an agent
     * 
     * @param string $username The user username
     * 
     * @return bool true if the user is an agent, false otherwise
     */
    public static function isAgent(string $username): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Agent WHERE username = :username');
      $stmt->bindValue(':username', $username);
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
    }

    /**
     * Check if the user is an admin
     * 
     * @param string $username The user username
     * 
     * @return bool true if the user is an admin, false otherwise
     */
    public static function isAdmin(string $username): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Admin WHERE username = :username');
      $stmt->bindValue(':username', $username);
      $stmt->execute();

      $result = $stmt->fetch();

      return $result !== false;
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
    public function __construct(string $username, string $password) {
      parent::__construct($username, $password);
    }
  }

  class Agent extends User {
    public function __construct(string $username, string $password) {
      if (!User::isAgent($username)) {
        throw new Exception('User is not an agent');
      }
      
      parent::__construct($username, $password);
    }
  }

  class Admin extends User {
    public function __construct(string $username, string $password) {
      if (!User::isAdmin($username)) {
        throw new Exception('User is not an admin');
      }

      parent::__construct($username, $password);
    }
  }
?>
