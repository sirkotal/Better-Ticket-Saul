const tickets_status = document.querySelectorAll('#tickets .status')

for (const ticket_status of tickets_status){
    ticket_status.style.backgroundColor = ticket_status.getAttribute('data-color');
}