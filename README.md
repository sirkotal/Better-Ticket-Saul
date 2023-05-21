# Better Ticket Saul

Group project for the "Linguagens e Tecnologias Web" course unit - development of a website to streamline and manage trouble tickets effectively. 

## Login Credentials

test_client/mydealerhasahummer

test_agent/brotherdontlikecellphones

test_agent_2/viktor

test_admin/saulgone

## API Routes

- /api
    - GET: Checks if API is working
- /api/admins
    - POST: promotes a user to admin
    - DELETE: demotes a admin to agent
- /api/agents
    - POST: promotes a client to agent
    - PUT: (/api/agents/{id}) adds or remove an agent with an id {id} to/from a department
    - DELETE: demotes a agento to client
- /api/departments
    - GET: gets all departments (if /api/departmens/{id} gets only the info of the department with the id {id})
    - POST: creates a new department
    - PUT: (/api/departments/{id}) renames a department with the id {id}
    - DELETE: (/api/departments/{id}) deletes a department with the id {id}
- /api/faq
    - GET: gets all faq (if /api/faq/{id} gets only the info of the faq with the id {id})
    - POST: creates a new faq
    - PUT: (/api/faq/{id}) edits a faq with the id {id}
    - DELETE: (/api/faq/{id}) deletes a faq with the id {id}
- /api/hashtags
    - GET: gets all hashtags (if /api/hashtags/{id} gets only the info of the hashtag with the id {id})
    - POST: creates a new hashtag
    - PUT: (/api/hashtags/{id}) edits a hashtag with the id {id}
    - DELETE: (/api/hashtags/{id}) deletes a hashtag with the id {id}
- /api/login
    - POST: logs in a user
- /api/logout
    - POST: logs out a user
- /api/logs
    - GET: gets all logs (if /api/logs/{id} gets only the info of the log with the id {id})
    - POST: creates a new log
    - PUT: (/api/logs/{id}) edits a log with the id {id}
    - DELETE: (/api/logs/{id}) deletes a log with the id {id}
- /api/register
    - POST: registers a new user
- /api/replies
    - GET: gets all replies (if /api/replies/{id} gets only the info of the reply with the id {id})
    - POST: creates a new reply
    - PUT: (/api/replies/{id}) edits a reply with the id {id}
    - DELETE: (/api/replies/{id}) deletes a reply with the id {id}
- /api/status (used internally)
    - GET: gets all status and color (if /api/status?status={status} gets only the info of the status {status})
- /api/tickets
    - GET: gets all tickets (if /api/tickets/{id} gets only the info of the ticket with the id {id}) (if /api/tickets/{id}/replies gets only the replies of the ticket with the id {id}, if /api/tickets/{id}/logs gets only the logs of the ticket with the id {id})
    - POST: creates a new ticket
    - PUT: (/api/tickets/{id}) edits a ticket with the id {id}
    - DELETE: (/api/tickets/{id}) deletes a ticket with the id {id}
- /api/users
    - GET: gets all users (if /api/users/{id} gets only the info of the user with the id {id})
    - PUT: (/api/users/{id}) edits a user with the id {id}
    - DELETE: (/api/users/{id}) deletes a user with the id {id}

## Instructions

Requires >= php8.0

Run the script [`create_db`](/create_db.sh) to create an example database.

Run the script [`run`](/run.sh) to run the website.

## Link to the Home page

http://localhost:9000

## Students Info

João Pedro Rodrigues Coutinho (up202108787)
Joaquim Afonso Marques da Cunha (up202108779)
Miguel Jorge Medeiros Garrido (up202108889)