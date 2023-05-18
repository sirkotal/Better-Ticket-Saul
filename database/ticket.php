<?php
  declare (strict_types = 1);

  require_once(__DIR__ . '/connection.php');
  require_once(__DIR__ . '/user.php');
  require_once(__DIR__ . '/department.php');
  require_once(__DIR__ . '/hashtag.php');
  require_once(__DIR__ . '/ticket_reply.php');
  require_once(__DIR__ . '/ticket_log.php');

  //? maybe change throws to something else

  class TicketStatus {
    public static function create(string $status, string $color): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO TicketStatus (status, color) VALUES (:status, :color)');
      $stmt->bindValue(':status', $status);
      $stmt->bindValue(':color', $color);
      $stmt->execute();
    }

    public static function getAll(): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM TicketStatus');
      $stmt->execute();

      $result = $stmt->fetchAll();

      return array_map(function ($row) {
        return [
          'status' => $row['status'],
          'color' => $row['color']
        ];
      }, $result);
    }

    public static function getColor(string $status): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM TicketStatus WHERE status = :status');
      $stmt->bindValue(':status', $status);
      $stmt->execute();

      $result = $stmt->fetch();

      if (!$result) {
        throw new Exception('Status not found');
      }

      return [
        'status' => $result['status'],
        'color' => $result['color']
      ];
    }

    public static function update(string $status, string $color): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE TicketStatus SET color = :color WHERE status = :status');
      $stmt->bindValue(':status', $status);
      $stmt->bindValue(':color', $color);
      $stmt->execute();
    }

    public static function delete(string $status): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketStatus WHERE status = :status');
      $stmt->bindValue(':status', $status);
      $stmt->execute();
    }
  }

  class Ticket {
    private int $id;
    private string $title;
    private string $text;
    private int $date;
    private string $status;
    private string|null $priority;
    private int|null $clientId;
    private int|null $agentId;
    private int|null $departmentId;
    private array $hashtagsIds = [];
    private array $repliesIds = [];
    private array $logsIds = [];

    public function __construct(int $id) {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT * FROM Ticket WHERE id = :id');
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetch();

      if (!$result) {
        throw new Exception('Ticket not found');
      }

      // check if status exists
      try {
        TicketStatus::getColor($result['status']);
      } catch (Exception $e) {
        throw new Exception('Ticket status not found');
      }

      $this->id = $result['id'];
      $this->title = $result['title'];
      $this->text = $result['text'];
      $this->date = $result['date'];
      $this->status = $result['status'];
      $this->priority = $result['priority'];
      $this->clientId = (int) $result['clientId'];
      $this->agentId = (int) $result['agentId'];
      $this->departmentId = (int) $result['departmentId'];

      $stmt = $db->prepare('SELECT hashtagId FROM TicketHashtag WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->hashtagsIds = array_map(function ($row) {
        return (int) $row['hashtagId'];
      }, $result);

      $stmt = $db->prepare('SELECT id FROM TicketReply WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->repliesIds = array_map(function ($row) {
        return (int) $row['id'];
      }, $result);

      $stmt = $db->prepare('SELECT id FROM TicketLog WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      $this->logsIds = array_map(function ($row) {
        return (int) $row['id'];
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
     * @param int $client The ticket's client id.
     * @param array $ticket_hashtags The ticket's hashtags.
     * @param int|null $department The ticket's department id. (optional)
     * @return Ticket The created ticket.
     */
    public static function create(string $title, string $text, int $clientId, array $ticket_hashtags, int|null $departmentId = null): Ticket {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('INSERT INTO Ticket (title, text, date, status, clientId) VALUES (:title, :text, :date, :status, :clientId)');
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':text', $text);
      $stmt->bindValue(':date', time(), PDO::PARAM_INT);
      $stmt->bindValue(':status', 'Open');
      $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
      $stmt->execute();

      $ticket_id = (int) $db->lastInsertId();

      if ($departmentId !== null) {
        $stmt = $db->prepare('UPDATE Ticket SET departmentId = :departmentId WHERE id = :id');
        $stmt->bindValue(':departmentId', $departmentId, PDO::PARAM_INT);
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
      $client = $this->getClient();
      $agent = $this->getAgent();
      $department = $this->getDepartment();
      $replies = $this->getReplies();
      $logs = $this->getLogs();

      return [
        'id' => $this->id,
        'title' => $this->title,
        'text' => $this->text,
        'date' => $this->date,
        'status' => $this->status,
        'priority' => $this->priority ? $this->priority : null,
        'client' => $this->clientId ? [
          'username' => $client->getUsername(),
          'name' => $client->getName(),
          'email' => $client->getEmail()
        ] : null,
        'agent' => $this->agentId ? [
          'username' => $agent->getUsername(),
          'name' => $agent->getName(),
          'email' => $agent->getEmail()
        ] : null,
        'department' => $this->departmentId ? [
          'name' => $department->getName()
        ] : null,
        'hashtags' => $this->getHashtags(),
        'replies' => array_map(function ($reply) {
          $info = $reply->parseJsonInfo();
          unset($info['id']);
          unset($info['ticket']);
          return $info;
        }, $replies),
        'logs' => array_map(function ($log) {
          $info = $log->parseJsonInfo();
          unset($info['id']);
          unset($info['ticket']);
          return $info;
        }, $logs)
      ];
    }

    /**
     * Get the tickets of a client.
     * 
     * @param Client $client The client.
     * @return array The tickets.
     */
    public static function getTicketsByClient(Client $client): array {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('SELECT id FROM Ticket WHERE clientId = :clientId');
      $stmt->bindValue(':clientId', $client->getId(), PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll();

      return array_map(function ($row) {
        return new Ticket($row['id']);
      }, $result);
    }

    /**
     * Assigns an agent to the ticket.
     * 
     * @param Agent $agent The agent to assign.
     */
    public function assignAgent(Agent $agent): void {
      if ($this->departmentId === null) {
        throw new Exception('The ticket does not have a department.');
      }

      if (!Department::isAgentFromDepartment($agent, new Department($this->departmentId))) {
        throw new Exception('The agent is not from the ticket\'s department.');
      }

      $db = getDatabaseConnection();

      $agent_id = $agent->getId();

      $stmt = $db->prepare('UPDATE Ticket SET agentId = :agentId WHERE id = :id');
      $stmt->bindValue(':agentId', $agent_id, PDO::PARAM_INT);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->agentId = $agent_id;
    }

    /**
     * Assigns a department to the ticket.
     * 
     * @param Department $department The department to assign.
     */
    public function assignDepartment(Department $department): void {
      if (!Department::isAgentFromDepartment(new Agent($this->agentId), $department)) {
        throw new Exception('The agent is not from the desired department.');
      }

      $db = getDatabaseConnection();

      $department_id = $department->getId();

      $stmt = $db->prepare('UPDATE Ticket SET departmentId = :departmentId WHERE id = :id');
      $stmt->bindValue(':departmentId', $department_id, PDO::PARAM_INT);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->departmentId = $department_id;
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

      $this->agentId = null;
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

      $this->departmentId = null;
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

      if (in_array($hashtag_id, $this->hashtagsIds)) {
        throw new Exception('The ticket already has the hashtag.');
      }

      $stmt = $db->prepare('INSERT INTO TicketHashtag (ticketId, hashtagId) VALUES (:ticketId, :hashtagId)');
      $stmt->bindValue(':ticketId', $this->id, PDO::PARAM_INT);
      $stmt->bindValue(':hashtagId', $hashtag_id, PDO::PARAM_INT);
      $stmt->execute();

      $this->hashtagsIds[] = $hashtag_id;
    }

    /**
     * Removes a hashtag from the ticket.
     * 
     * @param string $hashtag The hashtag to remove.
     */
    public function removeHashtag(string $hashtag): void {
      //! check if frontend can send the id instead of the hashtag

      $hashtags = new Hashtag();
      $hashtags = $hashtags->getHashtags();
      $hashtag_id = array_search($hashtag, $hashtags) + 1;

      if (!in_array($hashtag_id, $this->hashtagsIds)) {
        throw new Exception('The ticket already has the hashtag.');
      }

      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE ticketId = :ticketId AND hashtagId = :hashtagId');
      $stmt->bindValue(':ticketId', $this->id, PDO::PARAM_INT);
      $stmt->bindValue(':hashtagId', $hashtag_id, PDO::PARAM_INT);
      $stmt->execute();

      $this->hashtagsIds = array_diff($this->hashtagsIds, [$hashtag_id]);
    }

    /**
     * Sets the ticket's title.
     * 
     * @param string $title The ticket's title.
     */
    public function setTitle(string $title): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET title = :title WHERE id = :id');
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->title = $title;
    }

    /**
     * Sets the ticket's text.
     * 
     * @param string $text The ticket's text.
     */
    public function setText(string $text): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('UPDATE Ticket SET text = :text WHERE id = :id');
      $stmt->bindValue(':text', $text);
      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->text = $text;
    }

    /**
     * Sets the ticket's hashtags.
     * 
     * @param array $hashtags The ticket's hashtags ids.
     */
    public function setHashtags(array $hashtags): void {
      $db = getDatabaseConnection();

      $stmt = $db->prepare('DELETE FROM TicketHashtag WHERE ticketId = :ticketId');
      $stmt->bindValue(':ticketId', $this->id, PDO::PARAM_INT);
      $stmt->execute();

      $this->hashtagsIds = [];
      $hashtagDb = new Hashtag();
      foreach ($hashtags as $hashtag) {
        $this->addHashtag($hashtagDb->getHashtagById($hashtag));
      }
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
     * @return Client The ticket's client (can be null).
     */
    public function getClient(): Client|null {
      return $this->clientId ? new Client($this->clientId) : null;
    }

    /**
     * Returns the ticket's agent.
     *  
     * @return Agent The ticket's agent. (can be null)
     */
    public function getAgent(): Agent|null {
      return $this->agentId ? new Agent($this->agentId) : null;
    }

    /**
     * Returns the ticket's department.
     * 
     * @return Department The ticket's department. (can be null)
     */
    public function getDepartment(): Department|null {
      return $this->departmentId ? new Department($this->departmentId) : null;
    }

    /**
     * Returns the ticket's hashtags.
     * 
     * @return array The ticket's hashtags.
     */
    public function getHashtags(): array {
      $hashtags = new Hashtag();
      $hashtags = $hashtags->getHashtags();

      $ticketHashtags = [];
      foreach ($this->hashtagsIds as $hashtagId) {
        $ticketHashtags[] = $hashtags[$hashtagId];
      }

      return $ticketHashtags;
    }

    /**
     * Returns the ticket's replies.
     * 
     * @return array The ticket's replies.
     */
    public function getReplies(): array {
      return array_map(function($replyId) {
        return new TicketReply($replyId);
      }, $this->repliesIds);
    }

    /**
     * Returns the ticket's logs.
     * 
     * @return array The ticket's logs.
     */
    public function getLogs(): array {
      return array_map(function($logId) {
        return new TicketLog($logId);
      }, $this->logsIds);
    }
  }
?>
