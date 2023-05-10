<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');

  class Hashtag {
    private array $hashtags;

    public function __construct() {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Hashtag');
      $stmt->execute();

      $result = $stmt->fetchAll();

      foreach ($result as $row) {
        $this->hashtags[$row['id']] = $row['hashtag'];
      }
    }

    /**
     * Check if a hashtag exists (finds a hashtag with the given name)
     * 
     * @param string $hashtag The hashtag
     * @return bool true if the hashtag exists, false otherwise
     */
    public static function exists(string $hashtag): bool {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Hashtag WHERE hashtag = :hashtag');
      $stmt->bindValue(':hashtag', $hashtag);
      $stmt->execute();

      $result = $stmt->fetch();
      return $result !== false;
    }

    /**
     * Add a hashtag
     * 
     * @param string $hashtag The hashtag
     */
    public function addHashtag(string $hashtag): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Hashtag (hashtag) VALUES (:hashtag)');
      $stmt->bindValue(':hashtag', $hashtag);
      $stmt->execute();

      $this->hashtags[$db->lastInsertId()] = $hashtag;
    }

    /**
     * Remove a hashtag
     * 
     * @param int $id The hashtag id
     */
    public function removeHashtag(int $id): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Hashtag WHERE id = :id');
      $stmt->bindValue(':id', $id);
      $stmt->execute();

      unset($this->hashtags[$id]);
    }

    /**
     * Get a hashtag by id
     * 
     * @param int $id The hashtag id
     * @return string The hashtag
     */
    public function getHashtagById(int $id): string {
      return $this->hashtags[$id];
    }

    /**
     * Get all the hashtags
     * 
     * @return array The hashtags
     */
    public function getHashtags(): array {
      return $this->hashtags;
    }
  }
?>
