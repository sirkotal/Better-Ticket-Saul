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
      menu.classList.toggle('expanded');
    });
  }
}  

function changeProfilePopUp(button, menu, body) {
  if (button !== null && menu !== null && body !== null) {
    button.addEventListener('click', () => {
      menu.classList.toggle('expanded');
      body.style.pointerEvents = 'none';
    });
  }
}

const hamburger_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');
hamburgerDropdown(hamburger_button, hamburger_menu);

const body = document.querySelector('body');
const ticket_button = document.querySelector('#ticket-button');
const ticket_menu = document.querySelector('#ticket-menu');
ticketDropdown(ticket_button, ticket_menu);

const edit_username = document.querySelector('.header-value #Username');
const username_popUp = document.querySelector('#username-change');
changeProfilePopUp(edit_username, username_popUp, body);

const edit_name = document.querySelector('.header-value #Name');
const name_popUp = document.querySelector('#name-change');
changeProfilePopUp(edit_name, name_popUp, body);

const edit_email = document.querySelector('.header-value #Email');
const email_popUp = document.querySelector('#email-change');
changeProfilePopUp(edit_email, email_popUp, body);