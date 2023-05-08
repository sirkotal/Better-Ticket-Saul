<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');
  require_once(__DIR__ . '/department.php');
  require_once(__DIR__ . '/hashtag.php');

  //? maybe change throws to something else

  class Ticket {
    private int $id;
    private string $title;
    private string $text;
    private int $date;
    private string $status;
    private string|null $priority;
    private Client $client;
    private Agent|null $agent = null;
    private Department|null $department = null;
    private array $hashtags;
    private array $replies;
    private array $logs;

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Ticket WHERE idTicket = :id');
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      $result = $stmt->fetch();

      if (!$result) {
        throw new Exception('Ticket not found');
      }

      $this->id = $result['idTicket'];
      $this->title = $result['title'];
      $this->text = $result['text'];
      $this->date = $result['date'];
      $this->status = $result['status'];
      $this->priority = $result['priority'];
      $this->client = new Client($result['client']);

      if ($result['agent'] !== null) {
        $this->agent = new Agent($result['agent']);
      }

      if ($result['department'] !== null) {
        $this->department = new Department($result['department']);
      }

      $stmt = $db->prepare('SELECT * FROM TicketHashtag WHERE idTicket = :ticket');
      $stmt->bindParam(':ticket', $id);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->hashtags = array_map(function ($row) {
        return $row['hashtag'];
      }, $result);

      $stmt = $db->prepare('SELECT * FROM TicketReply WHERE idTicket = :ticket');
      $stmt->bindParam(':ticket', $id);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->replies = array_map(function ($row) {
        return new TicketReply($row['idTicketReply']);
      }, $result);

      $stmt = $db->prepare('SELECT * FROM TicketLog WHERE idTicket = :ticket');
      $stmt->bindParam(':ticket', $id);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->logs = array_map(function ($row) {
        return new TicketLog($row['idTicketLog']);
      }, $result);
    }

    /**
     * Returns all tickets.
     * 
     * @return array An array of all tickets.
     */
    public static function getAllTickets(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT idTicket FROM Ticket');
      $stmt->execute();

      $result = $stmt->fetchAll();

      return array_map(function ($row) {
        return new Ticket($row['idTicket']);
      }, $result);
    }

    /**
     * Creates a new ticket.
     * 
     * @param string $title The ticket's title.
     * @param string $text The ticket's text.
     * @param Client $client The ticket's client.
     * @param array $ticket_hashtags The ticket's hashtags.
     * @param Department $department The ticket's department. (optional)
     */
    public static function create(string $title, string $text, Client $client, array $ticket_hashtags, Department $department = null): void {
      $db = getDatabaseConnection();

      $client_username = $client->getUsername();
      $date = time();
      $status = 'Open';

      $stmt = $db->prepare('INSERT INTO Ticket (title, text, date, status, client) VALUES (:title, :text, :date, :status, :client)');
      $stmt->bindParam(':title', $title);
      $stmt->bindParam(':text', $text);
      $stmt->bindParam(':date', $date);
      $stmt->bindParam(':status', $status);
      $stmt->bindParam(':client', $client_username);
      $stmt->execute();

      $ticket_id = $db->lastInsertId();

      if ($department !== null) {
        $department_name = $department->getName();

        $stmt = $db->prepare('UPDATE Ticket SET department = :department WHERE idTicket = :id');
        $stmt->bindParam(':department', $department_name);
        $stmt->bindParam(':id', $ticket_id);
        $stmt->execute();
      }

      if (!empty($ticket_hashtags)) {
        $stmt = $db->prepare('INSERT INTO TicketHashtag (idTicket, hashtag) VALUES (:ticket, :hashtag)');

        foreach ($ticket_hashtags as $hashtag) {
          $stmt->bindParam(':ticket', $ticket_id);
          $stmt->bindParam(':hashtag', $hashtag);
          $stmt->execute();
        }
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

      //? maybe add a cascade delete to the database

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE idTicket = :ticket');
      $stmt->bindParam(':ticket', $id);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM TicketReply WHERE idTicket = :ticket');
      $stmt->bindParam(':ticket', $id);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM TicketLog WHERE idTicket = :ticket');
      $stmt->bindParam(':ticket', $id);
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
     * @param string $priority The ticket's priority.
     */
    public function setPriority(string $priority): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET priority = :priority WHERE idTicket = :id');
      $stmt->bindParam(':priority', $priority);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $this->priority = $priority;
    }

    /**
     * Adds a hashtag to the ticket.
     * 
     * @param string $hashtag The hashtag to add.
     */
    public function addHashtag(string $hashtag): void {
      if (!Hashtag::exists($hashtag)) {
        throw new Exception('The hashtag does not exist.');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketHashtag (idTicket, hashtag) VALUES (:ticket, :hashtag)');
      $stmt->bindParam(':ticket', $this->id);
      $stmt->bindParam(':hashtag', $hashtag);
      $stmt->execute();

      $this->hashtags[] = $hashtag;
    }

    /**
     * Removes a hashtag from the ticket.
     * 
     * @param string $hashtag The hashtag to remove.
     */
    public function removeHashtag(string $hashtag): void {
      if (!in_array($hashtag, $this->hashtags)) {
        throw new Exception('The ticket does not have the hashtag.');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE idTicket = :ticket AND hashtag = :hashtag');
      $stmt->bindParam(':ticket', $this->id);
      $stmt->bindParam(':hashtag', $hashtag);
      $stmt->execute();

      $this->hashtags = array_diff($this->hashtags, [$hashtag]);
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
     * Returns the ticket's title.
     * 
     * @return string The ticket's title.
     */
    public function getTitle(): string {
      return $this->title;
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
     * @return string The ticket's status.
     */
    public function getStatus(): string {
      return $this->status;
    }

    /**
     * Returns the ticket's priority.
     * 
     * @return string The ticket's priority. (can be null)
     */
    public function getPriority(): string|null {
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

    /**
     * Returns the ticket's hashtags.
     * 
     * @return array The ticket's hashtags.
     */
    public function getHashtags(): array {
      return $this->hashtags;
    }

    /**
     * Returns the ticket's replies.
     * 
     * @return array The ticket's replies.
     */
    public function getReplies(): array {
      return $this->replies;
    }

    /**
     * Returns the ticket's logs.
     * 
     * @return array The ticket's logs.
     */
    public function getLogs(): array {
      return $this->logs;
    }
  }
?>
