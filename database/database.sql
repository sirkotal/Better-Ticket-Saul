DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS Agent;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS AgentDepartment;
DROP TABLE IF EXISTS Reply;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS TicketLog;
DROP TABLE IF EXISTS Hashtag;
DROP TABLE IF EXISTS TicketHashtag;
DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Faq;

CREATE TABLE User (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(25),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE Client (
    userId REFERENCES User(id)
);

CREATE TABLE Agent (
    userId REFERENCES Client(id)
);

CREATE TABLE Admin (
    userId REFERENCES Agent(id)
);

CREATE TABLE Department (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(25)
);

CREATE TABLE AgentDepartment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    agentId REFERENCES Agent(userId),
    departmentId REFERENCES Department(id)
);

CREATE TABLE Ticket (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR,
    text VARCHAR,
    date INTEGER,
    status VARCHAR,
    priority VARCHAR,
    clientId REFERENCES Client(userId),
    agentId REFERENCES Agent(userId),
    departmentId REFERENCES Department(id)
);

CREATE TABLE TicketReply (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reply VARCHAR,
    date INTEGER,
    -- attachment VARCHAR,
    ticketId INTEGER REFERENCES Ticket(id),
    authorId REFERENCES User(id)
);

CREATE TABLE TicketLog (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    change VARCHAR,
    date INTEGER,
    ticketId REFERENCES Ticket(id),
    agentId REFERENCES Agent(userId),
    departmentId REFERENCES Department(id)
);

CREATE TABLE TicketStatus (
    status VARCHAR,
    color VARCHAR
);

CREATE TABLE Hashtag (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hashtag VARCHAR
);

CREATE TABLE TicketHashtag (
    hashtagId REFERENCES Hashtag(id),
    ticketId REFERENCES Ticket(id)
);

CREATE TABLE Faq (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    question VARCHAR,
    answer VARCHAR
);
