<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');

  class FAQ {
    private array $questions;

    public function __construct() {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Faq');
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->questions = array_map(function ($row) {
        return [
          'id' => $row['id'],
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
     * Get a question by id
     * 
     * @param int $id The id of the question
     * @return array The question
     */
    public function getQuestionById(int $id): array {
      $index = array_search($id, array_column($this->questions, 'id'), true);

      if ($index === false) {
        throw new Exception('Question not found');
      }

      return $this->questions[$index];
    }

    /**
     * Add a question
     * 
     * @param string $question The question
     * @param string $anwser The answer
     * @return array The added question
     */
    public function addQuestion(string $question, string $anwser): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Faq (question, answer) VALUES (:question, :answer)');
      $stmt->bindValue(':question', $question);
      $stmt->bindValue(':answer', $anwser);
      $stmt->execute();

      $question = [
        'id' => $db->lastInsertId(),
        'question' => $question,
        'answer' => $anwser
      ];

      $this->questions[] = $question;

      return $question;
    }

    /**
     * Remove a question
     * 
     * @param int $id The id of the question
     * @return array The removed question
     */
    public function removeQuestion(int $id): array {
      $db = getDatabaseConnection();

      // Check if the question exists
      try {
        $question = FAQ::getQuestionById($id);
      } catch (Exception $e) {
        throw new Exception('Question not found');
      }

      $stmt = $db->prepare('DELETE FROM Faq WHERE id = :id');
      $stmt->bindValue(':id', $id);
      $stmt->execute();

      $this->questions = array_filter($this->questions, function ($question) use ($id) {
        return $question['id'] !== $id;
      });

      return $question;
    }
  }
?>
