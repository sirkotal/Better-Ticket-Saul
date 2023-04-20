<?php
  declare(strict_types=1);

  function getDatabaseConnection(): PDO {
    $db = new PDO('sqlite:' . __DIR__ . '/database.db');

    return $db;
  }
?>