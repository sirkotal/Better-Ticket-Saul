const menu_button = document.querySelector('#menu-button');

menu_button.addEventListener('click', () => {
    if (menu_button.classList.contains('expanded')) {
        menu_button.classList.remove('expanded');
    } else {
        menu_button.classList.add('expanded');
    }
});
