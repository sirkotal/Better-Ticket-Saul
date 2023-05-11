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
    private Client|null $client;
    private Agent|null $agent;
    private Department|null $department;
    private array $hashtags = [];
    private array $replies = [];
    private array $logs = [];

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Ticket WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      if (!$result) {
        throw new Exception('Ticket not found');
      }

      $this->id = $result['id'];
      $this->title = $result['title'];
      $this->text = $result['text'];
      $this->date = $result['date'];
      $this->status = $result['status'];
      $this->priority = $result['priority'];
      $this->client = $result['clientId'] !== null ? new Client($result['clientId']) : null;
      $this->agent = $result['agentId'] !== null ? new Agent($result['agentId']) : null;
      $this->department = $result['departmentId'] !== null ? new Department($result['departmentId']) : null;

      $stmt = $db->prepare('SELECT * FROM TicketHashtag WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      foreach ($result as $row) {
        $hashtags = new Hashtag();
        $this->hashtags[] = $hashtags->getHashtagById($row['hashtagId']);
      }

      $stmt = $db->prepare('SELECT * FROM TicketReply WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->replies = array_map(function ($row) {
        return new TicketReply($row['id']);
      }, $result);

      $stmt = $db->prepare('SELECT * FROM TicketLog WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->logs = array_map(function ($row) {
        return new TicketLog($row['id']);
      }, $result);
    }

    /**
     * Returns all tickets.
     * 
     * @return array An array of all tickets.
     */
    public static function getAllTickets(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT id FROM Ticket');
      $stmt->execute();

      $result = $stmt->fetchAll();

      return array_map(function ($row) {
        return new Ticket($row['id']);
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
     * @return Ticket The created ticket.
     */
    public static function create(string $title, string $text, Client $client, array $ticket_hashtags, Department $department = null): Ticket {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Ticket (title, text, date, status, clientId) VALUES (:title, :text, :date, :status, :clientId)');
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':text', $text);
      $stmt->bindValue(':date', time(), PDO::PARAM_INT);
      $stmt->bindValue(':status', 'Open');
      $stmt->bindValue(':clientId', $client->getId(), PDO::PARAM_INT);
      $stmt->execute();

      $ticket_id = (int) $db->lastInsertId();

      if ($department !== null) {
        $stmt = $db->prepare('UPDATE Ticket SET departmentId = :departmentId WHERE id = :id');
        $stmt->bindValue(':departmentId', $department->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();
      }

      if (!empty($ticket_hashtags)) {
        $stmt = $db->prepare('INSERT INTO TicketHashtag (ticketId, hashtagId) VALUES (:ticketId, :hashtagId)');

        //! check if we can get the hashtag id from the frontend
        $hashtags = new Hashtag();
        $hashtags = $hashtags->getHashtags();
        foreach ($ticket_hashtags as $hashtag) {
          // TODO: test later
          $hashtag_id = array_search($hashtag, $hashtags) + 1;

          $stmt->bindValue(':ticketId', $ticket_id, PDO::PARAM_INT);
          $stmt->bindValue(':hashtagId', $hashtag_id, PDO::PARAM_INT);
          $stmt->execute();
        }
      }

      return new Ticket($ticket_id);
    }

    /**
     * Delete the ticket.
     * 
     * @param int $id The ticket's id.
     * @return array The deleted ticket info.
     */
    public static function delete(int $id): array {
      $ticket = new Ticket($id);
      $info = $ticket->parseJsonInfo();
      
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM Ticket WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM TicketReply WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM TicketLog WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      return $info;
    }

    /**
     * Parse a user info to an array ready to be json encoded
     * 
     * @return array The parsed user info.
     */
    public function parseJsonInfo(): array {
      $repliesIds = [];
      foreach ($this->replies as $reply) {
        $repliesIds[] = $reply->getId();
      }

      $logsIds = [];
      foreach ($this->logs as $log) {
        $logsIds[] = $log->getId();
      }

      return [
        'id' => $this->id,
        'title' => $this->title,
        'text' => $this->text,
        'date' => $this->date,
        'status' => $this->status,
        'priority' => $this->priority,
        'clientId' => $this->client ? $this->client->getId() : null,
        'agentId' => $this->agent ? $this->agent->getId() : null,
        'departmentId' => $this->department ? $this->department->getId() : null,
        'hashtags' => $this->hashtags,
        'repliesIds' => $repliesIds,
        'logsIds' => $logsIds
      ];
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

      $agent_id = $agent->getId();

      $stmt = $db->prepare('UPDATE Ticket SET agentId = :agentId WHERE id = :id');
      $stmt->bindValue(':agentId', $agent_id, PDO::PARAM_INT);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
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

      $department_id = $department->getId();

      $stmt = $db->prepare('UPDATE Ticket SET departmentId = :departmentId WHERE id = :id');
      $stmt->bindValue(':departmentId', $department_id, PDO::PARAM_INT);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
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

      $stmt = $db->prepare('UPDATE Ticket SET agentId = NULL WHERE id = :id');
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
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

      $stmt = $db->prepare('UPDATE Ticket SET departmentId = NULL WHERE id = :id');
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->department = null;
    }

    /**
     * Sets the ticket's priority.
     * 
     * @param string|null $priority The ticket's priority.
     */
    public function setPriority(string|null $priority): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET priority = :priority WHERE id = :id');
      $stmt->bindValue(':priority', $priority);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->priority = $priority;
    }

    /**
     * Adds a hashtag to the ticket.
     * 
     * @param string $hashtag The hashtag to add.
     */
    public function addHashtag(string $hashtag): void {
      //! check if frontend can send the id instead of the hashtag

      if (!Hashtag::exists($hashtag)) {
        throw new Exception('The hashtag does not exist.');
      }

      $db = getDatabaseConnection();

      $hashtags = new Hashtag();
      $hashtags = $hashtags->getHashtags();
      $hashtag_id = array_search($hashtag, $hashtags) + 1;

      if (in_array($hashtag, $this->hashtags)) {
        throw new Exception('The ticket already has the hashtag.');
      }

      $stmt = $db->prepare('INSERT INTO TicketHashtag (ticketId, hashtagId) VALUES (:ticketId, :hashtagId)');
      $stmt->bindValue(':ticketId', $this->id, PDO::PARAM_INT);
      $stmt->bindValue(':hashtagId', $hashtag_id, PDO::PARAM_INT);
      $stmt->execute();

      $this->hashtags[] = $hashtag;
    }

    /**
     * Removes a hashtag from the ticket.
     * 
     * @param string $hashtag The hashtag to remove.
     */
    public function removeHashtag(string $hashtag): void {
      //! check if frontend can send the id instead of the hashtag

      if (!in_array($hashtag, $this->hashtags)) {
        throw new Exception('The ticket does not have the hashtag.');
      }

      $db = getDatabaseConnection();

      $hashtags = new Hashtag();
      $hashtags = $hashtags->getHashtags();
      $hashtag_id = array_search($hashtag, $hashtags) + 1;

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE ticketId = :ticketId AND hashtagId = :hashtagId');
      $stmt->bindValue(':ticketId', $this->id, PDO::PARAM_INT);
      $stmt->bindValue(':hashtagId', $hashtag_id, PDO::PARAM_INT);
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
