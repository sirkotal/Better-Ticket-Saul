<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/../database/user.php');

  class Session {
    public function __construct() {
      session_start();
    }

    /**
     * Check if the user is logged in
     * 
     * @return bool true if the user is logged in, false otherwise
     */
    public function isLoggedIn(): bool {
      return isset($_SESSION['user']);
    }

    /**
     * Log the user out by unsetting the user in the session
     */
    public function logout(): void {
      unset($_SESSION['user']);
    }

    /**
     * Set the user in the session
     * 
     * @param int $user_id the user id
     */
    public function setUser(int $user_id): void {
      $_SESSION['user'] = $user_id;
    }

    /**
     * Get the user in the session
     * 
     * @return User|null the user if it exists, null otherwise
     */
    public function getUser(): User|null {
      if (!isset($_SESSION['user'])) {
        return null;
      }

      return User::getUserById($_SESSION['user']);
    }

    /**
     * Set an error in the session
     * 
     * @param string $errorType the error type
     * @param string $errorMessage the error message
     */
    public function setError(string $errorType, string $errorMessage): void {
      $_SESSION[$errorType] = $errorMessage;
    }

    /**
     * Unset an error in the session
     * 
     * @param string $errorType the error type
     */
    public function unsetError(string $errorType): void {
      unset($_SESSION[$errorType]);
    }

    /**
     * Get an error in the session
     * 
     * @param string $errorType the error type
     * @return string|null The error message or null if there is no error.
     */
    public function getError(string $errorType): string|null {
      if (isset($_SESSION[$errorType])) {
        return $_SESSION[$errorType];
      }
      return null;
    }
  }
?>