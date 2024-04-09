# Better Ticket Saul

Group project for the "Linguagens e Tecnologias Web" course unit - development of a website to streamline and manage trouble tickets effectively. 

## Login Credentials

test_client/mydealerhasahummer

test_agent/brotherdontlikecellphones

test_agent_2/viktor

test_admin/saulgone

## API Routes

- `/api`
    - GET: Checks if the API is working
- `/api/admins`
    - POST: Promotes a user to admin
    - DELETE: Demotes an admin to agent
- `/api/agents`
    - POST: Promotes a client to agent
    - PUT: (`/api/agents/{id}`) Adds or removes an agent with an id {id} to/from a department
    - DELETE: Demotes an agent to client
- `/api/departments`
    - GET: Gets all departments (if `/api/departmens/{id}` only gets the info of the department with the id {id})
    - POST: Creates a new department
    - PUT: (`/api/departments/{id}`) Renames a department with the id {id}
    - DELETE: (`/api/departments/{id}`) Deletes a department with the id {id}
- `/api/faq`
    - GET: Gets all FAQ (if `/api/faq/{id}` only gets the info of the FAQ with the id {id})
    - POST: Creates a new FAQ
    - PUT: (`/api/faq/{id}`) Edits a FAQ with the id {id}
    - DELETE: (`/api/faq/{id}`) Deletes a FAQ with the id {id}
- `/api/hashtags`
    - GET: Gets all hashtags (if `/api/hashtags/{id}` only gets the info of the hashtag with the id {id})
    - POST: Creates a new hashtag
    - PUT: (`/api/hashtags/{id}`) Edits a hashtag with the id {id}
    - DELETE: (`/api/hashtags/{id}`) Deletes a hashtag with the id {id}
- `/api/login`
    - POST: Logs in a user
- `/api/logout`
    - POST: Logs out a user
- `/api/logs`
    - GET: Gets all logs (if `/api/logs/{id}` only gets the info of the log with the id {id})
    - POST: Creates a new log
    - PUT: (`/api/logs/{id}`) Edits a log with the id {id}
    - DELETE: (`/api/logs/{id}`) Deletes a log with the id {id}
- `/api/register`
    - POST: Registers a new user
- `/api/replies`
    - GET: Gets all replies (if `/api/replies/{id}` only gets the info of the reply with the id {id})
    - POST: Creates a new reply
    - PUT: (`/api/replies/{id}`) Edits a reply with the id {id}
    - DELETE: (`/api/replies/{id}`) Deletes a reply with the id {id}
- `/api/status` (used internally)
    - GET: Gets all the statuses and colors (if `/api/status?status={status}` only gets the info of the status {status})
- `/api/tickets`
    - GET: Gets all tickets (if `/api/tickets/{id}` only gets the info of the ticket with the id {id}; if `/api/tickets/{id}/replies` gets only the replies of the ticket with the id {id}; if `/api/tickets/{id}/logs` gets only the logs of the ticket with the id {id})
    - POST: Creates a new ticket
    - PUT: (`/api/tickets/{id}`) Edits a ticket with the id {id}
    - DELETE: (`/api/tickets/{id}`) Deletes a ticket with the id {id}
- `/api/users`
    - GET: Gets all users (if `/api/users/{id}` gets only the info of the user with the id {id})
    - PUT: (`/api/users/{id}`) Edits a user with the id {id}
    - DELETE: (`/api/users/{id}`) Deletes a user with the id {id}

## Instructions

Requires >= php8.0

Run the script [`create_db`](/create_db.sh) to create an example database.

Run the script [`run`](/run.sh) to run the website.

## Link to the Home page

http://localhost:9000

## Students Info

#### João Pedro Rodrigues Coutinho (up202108787)
#### Joaquim Afonso Marques da Cunha (up202108779)
#### Miguel Jorge Medeiros Garrido (up202108889)
