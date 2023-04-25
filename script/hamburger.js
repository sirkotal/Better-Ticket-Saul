const menu_button = document.querySelector('#hamburger-button');
const hamburger_menu = document.querySelector('#hamburger-menu');

menu_button.addEventListener('click', () => {
    if (menu_button.classList.contains('expanded')) {
        menu_button.classList.remove('expanded');
        hamburger_menu.classList.remove('expanded');
    } else {
        menu_button.classList.add('expanded');
        hamburger_menu.classList.add('expanded');
    }
});
