<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');
  require_once(__DIR__ . '/department.php');
  require_once(__DIR__ . '/ticket.php');

  class TicketReply {
    private int $id;
    private string $reply;
    private int $date;
    private int $ticketId;
    private int $agentId;
    private int $departmentId;

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM TicketReply WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      if (!$result) {
        throw new Exception('Ticket reply not found');
      }

      $this->id = $result['id'];
      $this->reply = $result['reply'];
      $this->date = $result['date'];
      $this->ticketId = $result['ticketId'];
      $this->agentId = $result['agentId'];
      $this->departmentId = $result['departmentId'];
    }

    /**
     * Creates a new ticket reply.
     * 
     * @param string $reply The reply's text.
     * @param int $ticketId The ticket to reply to.
     * @param int $agentId The agent that replied.
     * @param int $departmentId The department of the agent.
     * @return TicketReply The created ticket reply.
     */
    public static function create(string $reply, int $ticketId, int $agentId, int $departmentId): TicketReply {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketReply (reply, date, ticketId, agentId, departmentId) VALUES (:reply, :date, :ticket, :agent, :department)');
      $stmt->bindValue(':reply', $reply);
      $stmt->bindValue(':date', time(), PDO::PARAM_INT);
      $stmt->bindValue(':ticket', $ticketId, PDO::PARAM_INT);
      $stmt->bindValue(':agent', $agentId, PDO::PARAM_INT);
      $stmt->bindValue(':department', $departmentId, PDO::PARAM_INT);
      $stmt->execute();

      return new TicketReply((int) $db->lastInsertId());
    }

    /**
     * Deletes a ticket reply.
     * 
     * @param int $id The reply's id.
     * @return array The deleted ticket reply info.
     */
    public static function delete(int $id): array {
      try {
        $ticketReply = new TicketReply($id);
      } catch (Exception $e) {
        throw new Exception('Ticket reply not found');
      }

      $info = $ticketReply->parseJsonInfo();

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketReply WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      return $info;
    }

    /**
     * Gets all ticket replies.
     * 
     * @return array All ticket replies.
     */
    public static function getAllReplies(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT id FROM TicketReply');
      $stmt->execute();
      $result = $stmt->fetchAll();

      $replies = [];

      foreach ($result as $reply) {
        $replies[] = new TicketReply((int) $reply['id']);
      }

      return $replies;
    }

    /**
     * Parse a ticket reply info to an array ready to be json encoded
     * 
     * @return array The parsed ticket reply info.
     */
    public function parseJsonInfo(): array {
      return [
        'id' => $this->id,
        'reply' => $this->reply,
        'date' => $this->date,
        'ticketId' => $this->ticketId,
        'agentId' => $this->agentId,
        'departmentId' => $this->departmentId
      ];
    }

    /**
     * Updates a ticket reply.
     * 
     * @param string $reply The reply's text.
     */
    public function update(string $reply): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE TicketReply SET reply = :reply WHERE id = :id');
      $stmt->bindValue(':reply', $reply);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->reply = $reply;
    }

    /**
     * Gets the reply's id.
     * 
     * @return int The reply's id.
     */
    public function getId(): int {
      return $this->id;
    }

    /**
     * Gets the reply's text.
     * 
     * @return string The reply's text.
     */
    public function getReply(): string {
      return $this->reply;
    }

    /**
     * Gets the reply's date.
     * 
     * @return int The reply's date.
     */
    public function getDate(): int {
      return $this->date;
    }

    /**
     * Gets the reply's ticket.
     * 
     * @return Ticket The reply's ticket.
     */
    public function getTicket(): Ticket {
      return new Ticket($this->ticketId);
    }

    /**
     * Gets the reply's agent.
     * 
     * @return Agent The reply's agent.
     */
    public function getAgent(): Agent {
      return new Agent($this->agentId);
    }

    /**
     * Gets the reply's department.
     * 
     * @return Department The reply's department.
     */
    public function getDepartment(): Department {
      return new Department($this->departmentId);
    }
  }
?>
