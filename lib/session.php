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
  }
?>
