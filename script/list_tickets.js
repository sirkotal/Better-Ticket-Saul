"use strict";

const ticketList = document.querySelector("#tickets");

async function createTicketRow(ticket) {
    // create the ticket div
    const ticketDiv = document.createElement("div");
    ticketDiv.classList.add("ticket");
    ticketDiv.setAttribute("data-id", ticket.id);

    // create the ticket title
    const ticketTitle = document.createElement("h2");
    ticketTitle.classList.add("title");
    const ticketLink = document.createElement("a");
    ticketLink.setAttribute("href", "#"); // TODO: change later
    ticketLink.textContent = ticket.title;
    ticketTitle.appendChild(ticketLink);

    // create the ticket bottom row
    const ticketBottom = document.createElement("div");
    ticketBottom.classList.add("bottom-row");

    // create the ticket status
    const ticketStatusColor = await fetch(`http://localhost:9000/api/status?status=${ticket.status}`)
        .then((response) => response.json())
        .then((data) => data.color);
    const ticketStatus = document.createElement("p");
    ticketStatus.textContent = "Status: ";
    const ticketStatusSpan = document.createElement("span");
    ticketStatusSpan.classList.add("status");
    ticketStatusSpan.style.backgroundColor = ticketStatusColor;
    ticketStatusSpan.textContent = ticket.status;
    ticketStatus.appendChild(ticketStatusSpan);
    ticketBottom.appendChild(ticketStatus);

    // create the ticket department
    const ticketDepartment = document.createElement("p");
    ticketDepartment.classList.add("department");
    ticketDepartment.textContent = ticket.department ? ticket.department.name : "No department";
    ticketBottom.appendChild(ticketDepartment);

    // create the ticket agent
    const ticketAgent = document.createElement("p");
    ticketAgent.classList.add("agent");
    ticketAgent.textContent = ticket.agent ? ticket.agent.name : "No agent";
    ticketBottom.appendChild(ticketAgent);

    // create the ticket date
    const ticketDate = document.createElement("p");
    ticketDate.classList.add("date");

    const date = new Date(ticket.date * 1000); // Multiply by 1000 to convert seconds to milliseconds
    ticketDate.textContent = date.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });;
    ticketBottom.appendChild(ticketDate);
    
    // append the ticket div to the ticket list
    ticketDiv.appendChild(ticketTitle);
    ticketDiv.appendChild(ticketBottom);
    ticketList.appendChild(ticketDiv);
}

async function getTickets() {
    const response = await fetch("http://localhost:9000/api/tickets");
    const data = await response.json();
    console.log(data);
    return data;
}

// js cant have top level await so we need to wrap it in a function
async function main() {
    const tickets = await getTickets();
    tickets.forEach(createTicketRow);
}
main();
