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

function changeProfilePopUp(buttons, menu, back) {
  if (buttons!== null && menu !== null && back !== null) {   
    const body = document.querySelector('#backgroud-popUp');
    const header = document.querySelector('header');
    let isExpanded = false;
    buttons.forEach((button) => {
      if (button !== null && menu !== null && body !== null) {
        
        button.addEventListener('click', () => {
          if (!isExpanded){  
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
    back.addEventListener('click', () => {
        isExpanded=false;
        menu.classList.remove('expanded');
        body.style.pointerEvents = 'auto';
        header.style.pointerEvents = 'auto';
        body.style.filter = 'grayscale(0)';
        header.style.filter = 'grayscale(0)';
    })
  }
}

const hamburger_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');
hamburgerDropdown(hamburger_button, hamburger_menu);

const ticket_button = document.querySelector('#ticket-button');
const ticket_menu = document.querySelector('#ticket-menu');
ticketDropdown(ticket_button, ticket_menu);

const username_button = document.querySelectorAll('.username-button');
const username_popUp = document.querySelector('#username-change');
const username_back = document.querySelector('#username-change .back-button');
changeProfilePopUp(username_button, username_popUp, username_back);

const name_button = document.querySelectorAll('.name-button');
const name_popUp = document.querySelector('#name-change');
const name_back = document.querySelector('#name-change .back-button');
changeProfilePopUp(name_button, name_popUp, name_back);

const email_button = document.querySelectorAll('.email-button');
const email_popUp = document.querySelector('#email-change');
const email_back = document.querySelector('#email-change .back-button');
changeProfilePopUp(email_button, email_popUp, email_back);

const password_button = document.querySelectorAll('.password-button');
const password_popUp = document.querySelector('#password-change');
const password_back = document.querySelector('#password-change .back-button');
changeProfilePopUp(password_button, password_popUp, password_back);
