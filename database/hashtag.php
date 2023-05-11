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
     * @return array Added hashtag
     */
    public function addHashtag(string $hashtag): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Hashtag (hashtag) VALUES (:hashtag)');
      $stmt->bindValue(':hashtag', $hashtag);
      $stmt->execute();

      $id = $db->lastInsertId();
      $this->hashtags[$id] = $hashtag;

      return [
        $id => $hashtag
      ];
    }

    /**
     * Remove a hashtag
     * 
     * @param int $id The hashtag id
     * @return array Removed hashtag
     */
    public function removeHashtag(int $id): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Hashtag WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE hashtagId = :hashtagId');
      $stmt->bindValue(':hashtagId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $info = [
        $id => $this->hashtags[$id]
      ];
      unset($this->hashtags[$id]);

      return $info;
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
