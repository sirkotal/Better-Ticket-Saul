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

      $stmt = $db->prepare('SELECT * FROM TicketLog WHERE idTicketLog = :id');
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      $result = $stmt->fetch();

      $this->id = $result['idTicketLog'];
      $this->change = $result['change'];
      $this->date = $result['date'];
      $this->ticket = new Ticket($result['idTicket']);
      $this->agent = new Agent($result['agent']);
      $this->department = new Department($result['department']);
    }

    /**
     * Creates a new ticket log.
     * 
     * @param string $change The change that was made.
     * @param Ticket $ticket The ticket that was changed.
     * @param Agent $agent The agent that made the change.
     * @param Department $department The department of the agent.
     */
    public static function create(string $change, Ticket $ticket, Agent $agent, Department $department): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketLog (change, date, idTicket, agent, department) VALUES (:change, :date, :idTicket, :agent, :department)');
      $stmt->bindValue(':change', $change);
      $stmt->bindValue(':date', time());
      $stmt->bindValue(':idTicket', $ticket->getId());
      $stmt->bindValue(':agent', $agent->getUsername());
      $stmt->bindValue(':department', $department->getName());
      $stmt->execute();
    }

    /**
     * Deletes a ticket log.
     * 
     * @param int $id The log's id.
     */
    public static function delete(int $id): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketLog WHERE idTicketLog = :id');
      $stmt->bindParam(':id', $id);
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