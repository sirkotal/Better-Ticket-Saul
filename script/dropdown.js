const hamburger_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');

if (hamburger_button !== null && hamburger_menu !== null) {
    hamburger_button.addEventListener('click', () => {
        hamburger_button.classList.toggle('expanded');
        hamburger_menu.classList.toggle('expanded');
    });
}

const ticket_button = document.querySelector('#ticket-button');
const ticket_menu = document.querySelector('#ticket-menu');

if (ticket_button !== null && ticket_menu !== null) {
    ticket_button.addEventListener('click', () => {
        ticket_menu.classList.toggle('expanded');
    });
}
