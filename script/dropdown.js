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
      button.addEventListener('mouseenter', () => {
        menu.classList.add('expanded');
        button.style.marginBottom = '-5rem';
        button.style.paddingBottom = '6rem';
      });
      button.addEventListener('mouseleave', () => {
          menu.classList.remove('expanded');
          button.style.marginBottom = '0';
          button.style.paddingBottom = '1rem';
      });
      menu.addEventListener('mouseenter', () => {
        menu.classList.add('expanded');
      });
      menu.addEventListener('mouseleave', () => {
        menu.classList.remove('expanded');
      });
    }
}

function signEffect(login, register, sign_div) {
    if (login !== null){
        login.addEventListener('mouseenter', () => {
          login.style.marginRight = '-3rem';
          sign_div.style.gap = '0';
          login.style.zIndex = '1';
          register.style.zIndex = '0';
        })
        login.addEventListener('mouseleave', () => {
          login.style.marginRight = '0';
          sign_div.style.gap = '1rem';
          login.style.zIndex = '0';
          register.style.zIndex = '1';
        })
    }
    if (register !== null){
      register.addEventListener('mouseenter', () => {
          register.style.marginLeft = '-3rem';
          sign_div.style.gap = '0';
      })
      register.addEventListener('mouseleave', () => {
          register.style.marginLeft = '0';
          sign_div.style.gap = '1rem';
      })
    }
}

const hamburger_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');
hamburgerDropdown(hamburger_button, hamburger_menu);
  
const ticket_button = document.querySelector('#ticket-button');
const ticket_menu = document.querySelector('#ticket-menu');
ticketDropdown(ticket_button, ticket_menu);

const login_button = document.querySelector('#signin');
const register_button = document.querySelector('#signup');
const sign_div = document.querySelector('#sign');
signEffect(login_button, register_button, sign_div);





