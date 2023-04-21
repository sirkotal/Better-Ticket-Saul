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
      return isset($_SESSION['user_username']);
    }

    /**
     * Log the user out by destroying the session
     */
    public function logout(): void {
      session_destroy();
    }

    /**
     * Set the user username in the session
     * 
     * @param string $user_username the user username
     */
    public function setUserUsername(string $user_username): void {
      $_SESSION['user_username'] = $user_username;
    }
  }
?>
