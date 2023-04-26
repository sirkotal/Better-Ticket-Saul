<?php
  declare (strict_types = 1);

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
     * @param string $user_username the user username
     */
    public function setUser(string $user_username): void {
      $_SESSION['user'] = $user_username;
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
  }
?>