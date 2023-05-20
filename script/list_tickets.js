"use strict";

const ticketList = document.querySelector("#tickets");
const departmentFilter = document.querySelector("#filter-department");
const statusFilter = document.querySelector("#filter-status");

// add status and department options to the filter
async function addOptionsToFilter() {
    const statusResponse = await fetch("http://localhost:9000/api/status");
    const statusData = await statusResponse.json();
    statusData.forEach((status) => {
        const option = document.createElement("option");
        option.value = status.status;
        option.textContent = status.status;
        statusFilter.appendChild(option);
    });

    const departmentResponse = await fetch("http://localhost:9000/api/departments");
    const departmentData = await departmentResponse.json();
    departmentData.forEach((department) => {
        const option = document.createElement("option");
        option.value = department.name;
        option.textContent = department.name;
        departmentFilter.appendChild(option);
    });
}

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
    return data;
}

// js cant have top level await so we need to wrap it in a function
async function main() {
    await addOptionsToFilter();
    const tickets = await getTickets();

    tickets.forEach(createTicketRow);

    // filter when changing the department or status
    departmentFilter.addEventListener("change", () => {
        // save the upper row so we can remove it later
        const upperRow = document.querySelector(".upper-row");

        if (departmentFilter.value === "all") {
            ticketList.innerHTML = "";
            ticketList.appendChild(upperRow);
            tickets.forEach(createTicketRow);
            return;
        }

        const filteredTickets = tickets.filter((ticket) => {
            if (ticket.department) {
                return ticket.department.name === departmentFilter.value;
            }
        });
        ticketList.innerHTML = "";
        ticketList.appendChild(upperRow);
        filteredTickets.forEach(createTicketRow);
    });

    statusFilter.addEventListener("change", () => {
        // save the upper row so we can remove it later
        const upperRow = document.querySelector(".upper-row");

        if (statusFilter.value === "all") {
            ticketList.innerHTML = "";
            ticketList.appendChild(upperRow);
            tickets.forEach(createTicketRow);
            return;
        }

        const filteredTickets = tickets.filter((ticket) => ticket.status === statusFilter.value);
        ticketList.innerHTML = "";
        ticketList.appendChild(upperRow);
        filteredTickets.forEach(createTicketRow);
    });
}
main();
