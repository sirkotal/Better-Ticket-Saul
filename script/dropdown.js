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
          login.style.marginLeft = '3rem';
          login.style.marginRight = '-3rem';
          login.style.zIndex = '1';
          register.style.zIndex = '0';
        })
        login.addEventListener('mouseleave', () => {
          login.style.marginRight = '0';
          login.style.marginLeft = '0';
          login.style.zIndex = '0';
          register.style.zIndex = '1';
        })
    }
    if (register !== null){
      register.addEventListener('mouseenter', () => {
          register.style.marginLeft = '-3rem';
          register.style.marginRight = '3rem';
      })
      register.addEventListener('mouseleave', () => {
          register.style.marginLeft = '0';
          register.style.marginRight = '0';
      })
    }
}

function faqDropdown() {
  const questions = document.querySelectorAll('.question');

  questions.forEach(section => {
    const question = section.querySelector('h2');
    const answer = section.querySelector('p');
    const button = section.querySelector('.dropdown-button');
    const icon = button.querySelector('i');

    button.addEventListener('click', () => {
      section.classList.toggle('open');
      button.classList.toggle('rotate');
      icon.classList.toggle('fa-caret-square-down');
      icon.classList.toggle('fa-caret-square-up');
    });
  });
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

faqDropdown();
