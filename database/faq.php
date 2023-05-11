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

      foreach ($result as $row) {
        $this->questions[(int) $row['id']] = [
          'question' => $row['question'],
          'answer' => $row['answer']
        ];
      }
    }

    /**
     * Parse a faq info to an array ready to be json encoded
     * 
     * @param int $faqId The id of the question
     * @return array The parsed faq info
     */
    public static function parseJsonInfo(int $faqId): array {
      $faq = new FAQ();
      
      return [
        'id' => $faqId,
        'question' => $faq->getQuestionById($faqId)['question'],
        'answer' => $faq->getQuestionById($faqId)['answer']
      ];
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
      if (!isset($this->questions[$id])) {
        throw new Exception('Question not found');
      }

      return $this->questions[$id];
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

      $questionId = $db->lastInsertId();

      $question = [
        'question' => $question,
        'answer' => $anwser
      ];

      $this->questions[$questionId] = $question;
      return FAQ::parseJsonInfo((int) $questionId);
    }

    /**
     * Remove a question
     * 
     * @param int $id The id of the question
     * @return array The removed question info
     */
    public function removeQuestion(int $id): array {
      $db = getDatabaseConnection();

      if (!isset($this->questions[$id])) {
        throw new Exception('Question not found');
      }

      $info = FAQ::parseJsonInfo($id);

      $stmt = $db->prepare('DELETE FROM Faq WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      unset($this->questions[$id]);
      return $info;
    }
  }
?>
