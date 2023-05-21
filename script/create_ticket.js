"use strict";

const button = document.getElementById("open-button");

async function createTicketJson() {
    const hashtags = document.querySelectorAll(".hashtag");
    const hashtagList = [];

    hashtags.forEach((element) => {
        hashtagList.push(element.innerHTML);
    });

    const ticketJson = {
        "title": document.getElementById("title").children[0].value,
        "text": document.getElementById("ticket-text").value,
        "clientId": parseInt(document.getElementById("client-id").value),
    };

    if (hashtagList.length > 0) {
        ticketJson.hashtags = hashtagList;
    }

    const department = document.getElementById("department").children[0].value;
    if (department !== "none") {
        ticketJson.departmentId = parseInt(department);
    }

    return ticketJson;
}

button.addEventListener("click", async () => {
    const ticketJson = await createTicketJson();

    console.log(ticketJson);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/tickets", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(ticketJson));
    xhr.onload = function() {
        if (xhr.status === 201) {
            alert("Ticket created successfully!");
            window.location.href = "/list_tickets.php";
        } else if (xhr.status === 400) {
            window.location.href = "/create_ticket.php";
            const data = JSON.parse(xhr.responseText);
            alert(data.error);
        }
    }
});
