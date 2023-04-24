<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');
  require_once(__DIR__ . '/department.php');

  //? maybe change throws to something else

  abstract class TicketStatus {
    const Open = 0;
    const InProgress = 1;
    const Closed = 2;
  }

  class Ticket {
    private int $id;
    private string $text;
    private int $date;
    private TicketStatus $status;
    private int|null $priority;
    private Client $client;
    private Agent|null $agent;
    private Department|null $department;

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Ticket WHERE idTicket = :id');
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      $result = $stmt->fetch();

      $this->id = $result['idTicket'];
      $this->text = $result['text'];
      $this->date = $result['date'];
      $this->status = $result['status'];
      $this->priority = $result['priority'];
      $this->client = new Client($result['client']);
      $this->agent = $result['agent'] !== null ? new Agent($result['agent']) : null;
      $this->department = $result['department'] !== null ? new Department($result['department']) : null;
    }

    /**
     * Creates a new ticket.
     * 
     * @param string $text The ticket's text.
     * @param Client $client The ticket's client.
     * @param Department $department The ticket's department. (optional)
     */
    public static function create(string $text, Client $client, Department $department = null): void {
      $db = getDatabaseConnection();

      $client_username = $client->getUsername();
      $date = time();
      $status = TicketStatus::Open;

      $stmt = $db->prepare('INSERT INTO Ticket (text, date, status, client) VALUES (:text, :date, :status, :client)');
      $stmt->bindParam(':text', $text);
      $stmt->bindParam(':date', $date);
      $stmt->bindParam(':status', $status);
      $stmt->bindParam(':client', $client_username);
      $stmt->execute();

      if ($department !== null) {
        $ticket_id = $db->lastInsertId(); //! needs testing
        $department_name = $department->getName();

        $stmt = $db->prepare('UPDATE Ticket SET department = :department WHERE idTicket = :id');
        $stmt->bindParam(':department', $department_name);
        $stmt->bindParam(':id', $ticket_id);
        $stmt->execute();
      }
    }

    /**
     * Delete the ticket.
     * 
     * @param int $id The ticket's id.
     */
    public static function delete(int $id): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Ticket WHERE idTicket = :id');
      $stmt->bindParam(':id', $id);
      $stmt->execute();
    }

    /**
     * Assigns an agent to the ticket.
     * 
     * @param Agent $agent The agent to assign.
     */
    public function assignAgent(Agent $agent): void {
      if ($this->agent !== null) {
        throw new Exception('The ticket already has an agent.');
      }

      if ($this->department === null) {
        throw new Exception('The ticket does not have a department.');
      }

      if (!Department::isAgentFromDepartment($agent, $this->department)) {
        throw new Exception('The agent is not from the ticket\'s department.');
      }

      $db = getDatabaseConnection();

      $agent_username = $agent->getUsername();

      $stmt = $db->prepare('UPDATE Ticket SET agent = :agent WHERE idTicket = :id');
      $stmt->bindParam(':agent', $agent_username);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $this->agent = $agent;
    }

    /**
     * Assigns a department to the ticket.
     * 
     * @param Department $department The department to assign.
     */
    public function assignDepartment(Department $department): void {
      if ($this->department !== null) {
        throw new Exception('The ticket already has a department.');
      }

      if (!Department::isAgentFromDepartment($this->agent, $department)) {
        throw new Exception('The agent is not from the ticket\'s department.');
      }

      $db = getDatabaseConnection();

      $department_name = $department->getName();

      $stmt = $db->prepare('UPDATE Ticket SET department = :department WHERE idTicket = :id');
      $stmt->bindParam(':department', $department_name);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $this->department = $department;
    }

    /**
     * Removes the ticket's agent.
     * 
     * @param Agent $agent The agent to remove.
     */
    public function removeAgent(): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET agent = NULL WHERE idTicket = :id');
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $this->agent = null;
    }

    /**
     * Removes the ticket's department.
     * 
     * @param Department $department The department to remove.
     */
    public function removeDepartment(): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET department = NULL WHERE idTicket = :id');
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $this->department = null;
    }

    /**
     * Sets the ticket's priority.
     * 
     * @param int $priority The ticket's priority.
     */
    public function setPriority(int $priority): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET priority = :priority WHERE idTicket = :id');
      $stmt->bindParam(':priority', $priority);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $this->priority = $priority;
    }

    /**
     * Returns the ticket's id.
     * 
     * @return int The ticket's id.
     */
    public function getId(): int {
      return $this->id;
    }

    /**
     * Returns the ticket's text.
     * 
     * @return string The ticket's text.
     */
    public function getText(): string {
      return $this->text;
    }

    /**
     * Returns the ticket's date in seconds since epoch.
     * 
     * @return int The ticket's date.
     */
    public function getDate(): int {
      return $this->date;
    }

    /**
     * Returns the ticket's status.
     * 
     * @return TicketStatus The ticket's status.
     */
    public function getStatus(): TicketStatus {
      return $this->status;
    }

    /**
     * Returns the ticket's priority.
     * 
     * @return int The ticket's priority. (can be null)
     */
    public function getPriority(): int|null {
      return $this->priority;
    }

    /**
     * Returns the ticket's client.
     * 
     * @return Client The ticket's client.
     */
    public function getClient(): Client {
      return $this->client;
    }

    /**
     * Returns the ticket's agent.
     *  
     * @return Agent The ticket's agent. (can be null)
     */
    public function getAgent(): Agent|null {
      return $this->agent;
    }

    /**
     * Returns the ticket's department.
     * 
     * @return Department The ticket's department. (can be null)
     */
    public function getDepartment(): Department|null {
      return $this->department;
    }
  }
?>
