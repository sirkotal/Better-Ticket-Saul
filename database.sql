-- Eliminacao de tabelas anteriores

DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS Agent;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Reply;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS TicketLog;
DROP TABLE IF EXISTS Hashtag;
DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Faq;

-- Criar tabelas

CREATE TABLE User (
    username VARCHAR(25) PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL UNIQUE
);

CREATE TABLE Client (
    username REFERENCES User(username) UNIQUE
);

CREATE TABLE Agent (
    username REFERENCES User(username) UNIQUE
);

CREATE TABLE Admin (
    username REFERENCES User(username) UNIQUE    
);

CREATE TABLE Department (
    name VARCHAR(25) PRIMARY KEY,
    agent REFERENCES Agent(username)
);

CREATE TABLE Reply (
    idreply INTEGER PRIMARY KEY,
    reply VARCHAR, 
    date INTEGER,
    attachment VARCHAR,
    idTicket INTEGER REFERENCES Ticket(idTicket),
    client REFERENCES Client(username),
    agent REFERENCES Agent(username),
);

CREATE TABLE Ticket (
     idTicket INTEGER PRIMARY KEY AUTOINCREMENT,
     text VARCHAR,
     date INTEGER,	
     status VARCHAR(10),
     priority INTEGER NOT NULL CHECK(priority >=1),
     client REFERENCES Client(username),
     agent REFERENCES Agent(username),
     department REFERENCES Department(name),
);

CREATE TABLE Hashtag (
     hashtag VARCHAR PRIMARY KEY
)

CREATE TABLE TicketHashtag (
     hashtag REFERENCES Hashtag(hashtag)
     idTicket REFERENCES Ticket(idTicket) 
)

CREATE TABLE Ticketlog (
     idTicketlog INTEGER PRIMARY KEY,
     date INTEGER,
     change VARCHAR,
     idticket REFERENCES Ticket(idTicket),
     agente REFERENCES Agent(username)
)

CREATE TABLE Faq (
     faq VARCHAR PRIMARY KEY
)

