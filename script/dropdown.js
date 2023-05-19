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
    isExpanded = false;
    buttons.forEach((button) => {
      if (button !== null && header!==null && body !== null) {
        button.addEventListener('click', () => {
          console.log(isExpanded);
          console.log('hello');
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



