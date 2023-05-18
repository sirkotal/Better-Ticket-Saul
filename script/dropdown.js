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

function changeProfilePopUp(buttons, menu) {
  const body = document.querySelector('#backgroud-popUp');
  const header = document.querySelector('header');
  let isExpanded = true;
  buttons.forEach((button) => {
    if (button !== null && menu !== null && body !== null) {
      
      button.addEventListener('click', () => {
        if (isExpanded){  
          menu.classList.add('expanded');
          body.style.pointerEvents = 'none';
          header.style.pointerEvents = 'none';
          body.style.filter = 'grayscale(1)';
          header.style.filter = 'grayscale(1)';
        }
        else {
          menu.classList.remove('expanded');
          body.style.pointerEvents = 'auto';
          header.style.pointerEvents = 'auto';
          body.style.filter = 'grayscale(0)';
          header.style.filter = 'grayscale(0)';
        }
        isExpanded = !isExpanded;     
      });
    }
  });
}

const hamburger_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');
hamburgerDropdown(hamburger_button, hamburger_menu);

const ticket_button = document.querySelector('#ticket-button');
const ticket_menu = document.querySelector('#ticket-menu');
ticketDropdown(ticket_button, ticket_menu);

const username_button = document.querySelectorAll('.username-button');
const username_popUp = document.querySelector('#username-change');
changeProfilePopUp(username_button, username_popUp);

const name_button = document.querySelectorAll('.name-button');
const name_popUp = document.querySelector('#name-change');
changeProfilePopUp(name_button, name_popUp);

const email_button = document.querySelectorAll('.email-button');
const email_popUp = document.querySelector('#email-change');
changeProfilePopUp(email_button, email_popUp);