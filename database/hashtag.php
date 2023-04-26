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

      $this->hashtags = array_map(function ($row) {
        return $row['hashtag'];
      }, $result);
    }

    /**
     * Check if a hashtag exists (finds a hashtag with the given name)
     * 
     * @param string $hashtag The hashtag
     * 
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
     * Get all the hashtags
     * 
     * @return array The hashtags
     */
    public function getHashtags(): array {
      return $this->hashtags;
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

      $this->hashtags[] = $hashtag;
    }

    /**
     * Remove a hashtag
     * 
     * @param string $hashtag The hashtag
     */
    public function removeHashtag(string $hashtag): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Hashtag WHERE hashtag = :hashtag');
      $stmt->bindValue(':hashtag', $hashtag);
      $stmt->execute();

      $this->hashtags = array_filter($this->hashtags, function ($h) use ($hashtag) {
        return $h !== $hashtag;
      });

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE hashtag = :hashtag');
      $stmt->bindValue(':hashtag', $hashtag);
      $stmt->execute();
    }
  }
?>
