<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');
  require_once(__DIR__ . '/department.php');
  require_once(__DIR__ . '/ticket.php');

  //? maybe have changes be an enum

  class TicketLog {
    private int $id;
    private string $change;
    private int $date;
    private int $ticketId;
    private int $agentId;
    private int $departmentId;

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM TicketLog WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      $this->id = $result['id'];
      $this->change = $result['change'];
      $this->date = $result['date'];
      $this->ticketId = (int) $result['ticketId'];
      $this->agentId = (int) $result['agentId'];
      $this->departmentId = (int) $result['departmentId'];
    }

    /**
     * Creates a new ticket log.
     * 
     * @param string $change The change that was made.
     * @param int $ticketId The id of ticket that was changed.
     * @param int $agentId The id of agent that made the change.
     * @param int $departmentId The id of department of the agent.
     * @return TicketLog The created ticket log.
     */
    public static function create(string $change, int $ticketId, int $agentId, int $departmentId): TicketLog {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketLog (change, date, ticketId, agentId, departmentId) VALUES (:change, :date, :idTicket, :agent, :department)');
      $stmt->bindValue(':change', $change);
      $stmt->bindValue(':date', time(), PDO::PARAM_INT);
      $stmt->bindValue(':idTicket', $ticketId, PDO::PARAM_INT);
      $stmt->bindValue(':agent', $agentId, PDO::PARAM_INT);
      $stmt->bindValue(':department', $departmentId, PDO::PARAM_INT);
      $stmt->execute();

      return new TicketLog((int) $db->lastInsertId());
    }

    /**
     * Deletes a ticket log.
     * 
     * @param int $id The log's id.
     * @return array The deleted log info.
     */
    public static function delete(int $id): array {
      $ticketLog = new TicketLog($id);
      $info = $ticketLog->parseJsonInfo();
      
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketLog WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      return $info;
    }

    /**
     * Gets all ticket logs.
     * 
     * @return array All ticket logs.
     */
    public static function getAllLogs(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT id FROM TicketLog');
      $stmt->execute();
      $result = $stmt->fetchAll();

      $logs = [];
      foreach ($result as $log) {
        $logs[] = new TicketLog((int) $log['id']);
      }

      return $logs;
    }

    /**
     * Parse a ticket log info to an array ready to be json encoded
     * 
     * @return array The parsed ticket log info.
     */
    public function parseJsonInfo(): array {
      return [
        'id' => $this->id,
        'change' => $this->change,
        'date' => $this->date,
        'ticketId' => $this->ticketId,
        'agentId' => $this->agentId,
        'departmentId' => $this->departmentId
      ];
    }

    /**
     * Updates the change of the log.
     * 
     * @param string $change The new change.
     */
    public function update(string $change): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE TicketLog SET change = :change WHERE id = :id');
      $stmt->bindValue(':change', $change);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();
    }

    /**
     * Gets the id of the log.
     * 
     * @return int The id of the log.
     */
    public function getId(): int {
      return $this->id;
    }

    /**
     * Gets the change that was made.
     * 
     * @return string The change that was made.
     */
    public function getChange(): string {
      return $this->change;
    }

    /**
     * Gets the date of the change.
     * 
     * @return int The date of the change.
     */
    public function getDate(): int {
      return $this->date;
    }

    /**
     * Gets the ticket that was changed.
     * 
     * @return Ticket The ticket that was changed.
     */
    public function getTicket(): Ticket {
      return new Ticket($this->ticketId);
    }

    /**
     * Gets the agent that made the change.
     * 
     * @return Agent The agent that made the change.
     */
    public function getAgent(): Agent {
      return new Agent($this->agentId);
    }

    /**
     * Gets the department of the agent.
     * 
     * @return Department The department of the agent.
     */
    public function getDepartment(): Department {
      return new Department($this->departmentId);
    }
  }
?>
