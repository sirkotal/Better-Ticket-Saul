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

INSERT INTO Client (username) VALUES ('test_client');
INSERT INTO Client (username) VALUES ('test_agent');
INSERT INTO Client (username) VALUES ('test_admin');

INSERT INTO Agent (username) VALUES ('test_agent');
INSERT INTO Agent (username) VALUES ('test_admin');

INSERT INTO Admin (username) VALUES ('test_admin');

-- Create departments

INSERT INTO Department (name) VALUES ('Sales');
INSERT INTO Department (name) VALUES ('Support');
INSERT INTO Department (name) VALUES ('Billing');

-- Add agents to departments

INSERT INTO AgentDepartment (agent, department) VALUES
    ('test_agent', 'Sales'),
    ('test_agent', 'Support'),
    ('test_agent_2', 'Billing');

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

-- Create Tickets

INSERT INTO Ticket (title, text, date, status, client) VALUES
    (
        'Sold my car and did not get paid',
        'I sold my car to a guy named Tuco Salamanca and he did not pay me. I want my money back.',
        1682731073,
        'Open',
        'test_client'
    );

INSERT INTO Ticket (title, text, date, status, client, department) VALUES
    (
        'I want to buy a car',
        'I want to buy a car, but it needs to be in cash. I have $100,000 in cash.',
        1682731073,
        'Open',
        'test_client',
        'Sales'
    );
