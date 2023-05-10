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
    password VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE Client (
    username REFERENCES User(username)
);

CREATE TABLE Agent (
    username REFERENCES Client(username)
);

CREATE TABLE Admin (
    username REFERENCES Agent(username)
);

CREATE TABLE AgentDepartment (
    idAgentDeparment INTEGER PRIMARY KEY AUTOINCREMENT,
    agent REFERENCES Agent(username),
    department REFERENCES Department(name)
);

CREATE TABLE Department (
    name VARCHAR(25) PRIMARY KEY
);

CREATE TABLE TicketReply (
    idReply INTEGER PRIMARY KEY AUTOINCREMENT,
    reply VARCHAR,
    date INTEGER,
    -- attachment VARCHAR,
    idTicket INTEGER REFERENCES Ticket(idTicket),
    agent REFERENCES Agent(username),
    department REFERENCES Department(name)
);

CREATE TABLE Ticket (
    idTicket INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR,
    text VARCHAR,
    date INTEGER,
    status VARCHAR,
    priority VARCHAR,
    client REFERENCES Client(username),
    agent REFERENCES Agent(username),
    department REFERENCES Department(name)
);

CREATE TABLE Hashtag (
    hashtag VARCHAR PRIMARY KEY
);

CREATE TABLE TicketHashtag (
    hashtag REFERENCES Hashtag(hashtag),
    idTicket REFERENCES Ticket(idTicket)
);

CREATE TABLE TicketLog (
    idTicketLog INTEGER PRIMARY KEY AUTOINCREMENT,
    change VARCHAR,
    date INTEGER,
    idTicket REFERENCES Ticket(idTicket),
    agent REFERENCES Agent(username),
    department REFERENCES Department(name)
);

CREATE TABLE Faq (
    faqId INTEGER PRIMARY KEY AUTOINCREMENT,
    question VARCHAR,
    answer VARCHAR
);
