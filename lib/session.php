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
      return isset($_SESSION['user_id']);
    }

    /**
     * Log the user out by destroying the session
     */
    public function logout(): void {
      session_destroy();
    }

    /**
     * Set the user id in the session
     * 
     * @param int $user_id the user id
     */
    public function setUserId(int $user_id): void {
      $_SESSION['user_id'] = $user_id;
    }
  }
?>
