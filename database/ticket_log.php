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
    private Ticket $ticket;
    private Agent $agent;
    private Department $department;

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM TicketLog WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      $this->id = $result['id'];
      $this->change = $result['change'];
      $this->date = $result['date'];
      $this->ticket = new Ticket($result['ticketId']);
      $this->agent = new Agent($result['agentId']);
      $this->department = new Department($result['departmentId']);
    }

    /**
     * Creates a new ticket log.
     * 
     * @param string $change The change that was made.
     * @param Ticket $ticket The ticket that was changed.
     * @param Agent $agent The agent that made the change.
     * @param Department $department The department of the agent.
     * @return TicketLog The created ticket log.
     */
    public static function create(string $change, Ticket $ticket, Agent $agent, Department $department): TicketLog {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketLog (change, date, ticketId, agentId, departmentId) VALUES (:change, :date, :idTicket, :agent, :department)');
      $stmt->bindValue(':change', $change);
      $stmt->bindValue(':date', time(), PDO::PARAM_INT);
      $stmt->bindValue(':idTicket', $ticket->getId(), PDO::PARAM_INT);
      $stmt->bindValue(':agent', $agent->getId(), PDO::PARAM_INT);
      $stmt->bindValue(':department', $department->getId(), PDO::PARAM_INT);
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
      $info = [
        'id' => $id,
        'change' => $ticketLog->getChange(),
        'date' => $ticketLog->getDate(),
        'ticketId' => $ticketLog->getTicket()->getId(),
        'agentId' => $ticketLog->getAgent()->getId(),
        'departmentId' => $ticketLog->getDepartment()->getId()
      ];
      
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketLog WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      return $info;
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
      return $this->ticket;
    }

    /**
     * Gets the agent that made the change.
     * 
     * @return Agent The agent that made the change.
     */
    public function getAgent(): Agent {
      return $this->agent;
    }

    /**
     * Gets the department of the agent.
     * 
     * @return Department The department of the agent.
     */
    public function getDepartment(): Department {
      return $this->department;
    }
  }
?>
