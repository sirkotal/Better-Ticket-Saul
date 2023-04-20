<?php
  declare(strict_types=1);

  function getDatabaseConnection(): PDO {
    $db = new PDO('sqlite:/database/database.db');

    return $db;
  }
?>