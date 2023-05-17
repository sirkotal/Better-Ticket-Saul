function hamburgerDropdown(button, menu) {
    if (button !== null && menu !== null) {
      button.addEventListener('click', () => {
        button.classList.toggle('expanded');
        menu.classList.toggle('expanded');
      });
    }
}

function ticketDropdown(button, menu) {
    if (button !== null && menu !== null) {
      button.addEventListener('click', () => {
        ticket_menu.classList.toggle('expanded');
      });
    }
}  

function changeProfilePopUp(button) {
  if (button !== null) {
      button.addEventListener('click', () => {
        button.classList.toggle('expanded')
      })
  }
}
  
const hamburger_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');
hamburgerDropdown(hamburger_button, hamburger_menu);
  
const ticket_button = document.querySelector('#ticket-button');
const ticket_menu = document.querySelector('#ticket-menu');
ticketDropdown(ticket_button, ticket_menu);

const edit_button = document.querySelector('#header-value #name');
changeProfilePopUp(edit_button);