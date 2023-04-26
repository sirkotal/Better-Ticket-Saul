<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');

  class FAQ {
    private array $questions;

    public function __construct() {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM FAQ');
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->questions = array_map(function ($row) {
        return [
          'id' => $row['faqId'],
          'question' => $row['question'],
          'answer' => $row['answer']
        ];
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
     * @param string $anwser The answer
     */
    public function addQuestion(string $question, string $anwser): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO FAQ (question, answer) VALUES (:question, :answer)');
      $stmt->bindValue(':question', $question);
      $stmt->bindValue(':answer', $anwser);
      $stmt->execute();

      $this->questions[] = [
        'id' => $db->lastInsertId(), //! needs testing
        'question' => $question,
        'answer' => $anwser
      ];
    }

    /**
     * Remove a question
     * 
     * @param int $id The id of the question
     */
    public function removeQuestion(int $id): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM FAQ WHERE faqId = :id');
      $stmt->bindValue(':id', $id);
      $stmt->execute();

      $this->questions = array_filter($this->questions, function ($question) use ($id) {
        return $question['id'] !== $id;
      });
    }
  }
?>
