-- Create users

INSERT INTO USER (username, name, email, password) VALUES
    (
        'test_client',
        'Vargas',
        'tucodontknow@email.com',
        'e46f771796253c04037b83cbff33bff2662c06c5' -- mydealerhasahummer
    ),
    (
        'test_agent',
        'Jim McGill',
        'oldladiesattorney@email.com',
        'e7f70078222a9f24185f501cd6e4944532a5f163' -- brotherdontlikecellphones
    ),
    (
        'test_agent_2',
        'Kim Wexler',
        'gisellesaintclaire@email.com',
        '86faeb3e05561b856666236e198c27e698275e82' -- viktor
    ),
    (
        'test_admin',
        'Saul Goodman',
        'bettercallsaul@email.com',
        '5f23a9f67b042c4ade26640c9b31af5a2541a627' -- saulgone
    );

-- Add roles to users

INSERT INTO Client (userId) VALUES (1); -- test_client
INSERT INTO Client (userId) VALUES (2); -- test_agent
INSERT INTO Client (userId) VALUES (3); -- test_agent_2
INSERT INTO Client (userId) VALUES (4); -- test_admin

INSERT INTO Agent (userId) VALUES (2); -- test_agent
INSERT INTO Agent (userId) VALUES (3); -- test_agent_2
INSERT INTO Agent (userId) VALUES (4); -- test_admin

INSERT INTO Admin (userId) VALUES (4); -- test_admin

-- Create departments

INSERT INTO Department (name) VALUES ('Sales');
INSERT INTO Department (name) VALUES ('Support');
INSERT INTO Department (name) VALUES ('Billing');

-- Add agents to departments

INSERT INTO AgentDepartment (agentId, departmentId) VALUES
    (2, 1), -- test_agent in Sales
    (2, 2), -- test_agent in Support
    (3, 2), -- test_agent_2 in Billing
    (3, 3); -- test_agent_2 in Billing


-- Create Hashtags

INSERT INTO Hashtag (hashtag) VALUES
    ('#test'),
    ('#lalo'),
    ('#tuco'),
    ('#mistawhite'),
    ('#hhm');

-- Create Faq

INSERT INTO Faq (question, answer) VALUES
    (
        'How do I create a ticket?',
        'You can create a ticket by clicking Ticket > Create Ticket in the navigation bar.'
    ),
    (
        'Why Better Call Saul?',
        'Because he is the best lawyer in Albuquerque.'
    );

-- Create Ticket Status

INSERT INTO TicketStatus (status, color) VALUES
    ('Open', '#008000'),
    ('In Progress', '#dd8000'),
    ('Closed', '#800000');

-- Create Tickets

INSERT INTO Ticket (title, text, date, status, clientId) VALUES
    (
        'Sold my car and did not get paid',
        'I sold my car to a guy named Tuco Salamanca and he did not pay me. I want my money back.',
        1682731073,
        'Open',
        1 -- test_client
    );

INSERT INTO Ticket (title, text, date, status, clientId, agentId, departmentId) VALUES
    (
        'I want to buy a car',
        'I want to buy a car, but it needs to be in cash. I have $100,000 in cash.',
        1682731073,
        'In Progress',
        1, -- test_client
        2, -- test_agent
        1 -- Sales
    );

INSERT INTO TicketHashtag (hashtagId, ticketId) VALUES
    (1, 1), -- #test
    (2, 1), -- #lalo
    (3, 1); -- #tuco

INSERT INTO TicketReply (reply, date, ticketId, agentId, departmentId) VALUES
    (
        'Hello, I am Jim McGill, your attorney. I will take care of this.',
        1682731073,
        2, -- I want to buy a car
        2, -- test_agent
        1 -- Sales
    );