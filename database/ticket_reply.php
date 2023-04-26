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
    private Ticket $ticket; //? maybe only get the client
    private Agent $agent; //? maybe store only the username in case the agent is deleted
    private Department $department; //? maybe store only the name in case the department is deleted

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM TicketReply WHERE idTicketReply = :id');
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      $result = $stmt->fetch();

      $this->id = $result['idTicketReply'];
      $this->reply = $result['reply'];
      $this->date = $result['date'];
      $this->ticket = new Ticket($result['idTicket']);
      $this->agent = new Agent($result['agent']);
      $this->department = new Department($result['department']);
    }

    /**
     * Creates a new ticket reply.
     * 
     * @param string $reply The reply's text.
     * @param Ticket $ticket The ticket to reply to.
     * @param Agent $agent The agent that replied.
     * @param Department $department The department of the agent.
     */
    public static function create(string $reply, Ticket $ticket, Agent $agent, Department $department): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketReply (reply, date, ticket, agent, department) VALUES (:reply, :date, :ticket, :agent, :department)');
      $stmt->bindValue(':reply', $reply);
      $stmt->bindValue(':date', time());
      $stmt->bindValue(':ticket', $ticket->getId());
      $stmt->bindValue(':agent', $agent->getUsername());
      $stmt->bindValue(':department', $department->getName());
      $stmt->execute();
    }

    /**
     * Deletes a ticket reply.
     * 
     * @param int $id The reply's id.
     */
    public static function delete(int $id): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketReply WHERE idTicketReply = :id');
      $stmt->bindParam(':id', $id);
      $stmt->execute();
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
      return $this->ticket;
    }

    /**
     * Gets the reply's agent.
     * 
     * @return Agent The reply's agent.
     */
    public function getAgent(): Agent {
      return $this->agent;
    }

    /**
     * Gets the reply's department.
     * 
     * @return Department The reply's department.
     */
    public function getDepartment(): Department {
      return $this->department;
    }
  }
?>
