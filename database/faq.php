<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');

  //? maybe add an id to the FAQ table
  class FAQ {
    private array $questions;

    public function __construct() {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM FAQ');
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->questions = array_map(function ($row) {
        return $row['faq'];
      }, $result);
    }

    /**
     * Get all the questions
     * 
     * @return array The questions
     */
    public function getQuestions(): array {
      return $this->questions;
    }

    /**
     * Add a question
     * 
     * @param string $question The question
     */
    public function addQuestion(string $question): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO FAQ (faq) VALUES (:question)');
      $stmt->bindValue(':question', $question);
      $stmt->execute();

      $this->questions[] = $question;
    }

    /**
     * Remove a question
     * 
     * @param string $question The question
     */
    public function removeQuestion(string $question): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM FAQ WHERE faq = :question');
      $stmt->bindValue(':question', $question);
      $stmt->execute();

      $this->questions = array_filter($this->questions, function ($q) use ($question) {
        return $q !== $question;
      });
    }
  }
?>
