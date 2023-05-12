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
     * @param string|int $key The hashtag name or id
     * @return bool true if the hashtag exists, false otherwise
     */
    public static function exists(string|int $key): bool {
      $db = getDatabaseConnection();

      if (is_int($key)) {
        $stmt = $db->prepare('SELECT * FROM Hashtag WHERE id = :id');
        $stmt->bindValue(':id', $key, PDO::PARAM_INT);
      } else {
        $stmt = $db->prepare('SELECT * FROM Hashtag WHERE hashtag = :hashtag');
        $stmt->bindValue(':hashtag', $key);
      }

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

      return $this->parseJsonInfo((int) $id);
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

      $info = $this->parseJsonInfo($id);
      unset($this->hashtags[$id]);

      return $info;
    }

    public function updateHashtag(int $id, string $hashtag): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Hashtag SET hashtag = :hashtag WHERE id = :id');
      $stmt->bindValue(':hashtag', $hashtag);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $this->hashtags[$id] = $hashtag;

      return $this->parseJsonInfo($id);
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
     * Parse the hashtag info to an array ready to be json encoded
     * 
     * @param int $id The hashtag id
     * @return array The parsed hashtag info.
     */
    public function parseJsonInfo(int $id): array {
      $info = [
        $id => $this->hashtags[$id]
      ];

      return $info;
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
