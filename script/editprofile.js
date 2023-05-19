function changeProfilePopUp(buttons, menu, back) {
  if (buttons!== null && menu !== null && back !== null) {   
    const body = document.querySelector('#backgroud-popUp');
    const header = document.querySelector('header');
    buttons.forEach((button) => {
      if (button !== null && header!==null && body !== null) {
        button.addEventListener('click', () => {
          menu.classList.add('expanded');
          body.style.pointerEvents = 'none';
          header.style.pointerEvents = 'none';
          body.style.filter = 'grayscale(1)';
          header.style.filter = 'grayscale(1)';   
        });
      }
    });
    back.addEventListener('click', () => {
        menu.classList.remove('expanded');
        body.style.pointerEvents = 'auto';
        header.style.pointerEvents = 'auto';
        body.style.filter = 'grayscale(0)';
        header.style.filter = 'grayscale(0)';
    })
  }
}

function update(saveButton, backButton, newValue, values, field) {
  if (saveButton !== null && backButton !== null && newValue !== null && values !== null && field !== null) { 
    saveButton.addEventListener('click', () => {
      paragraph = document.querySelector('.profile-change #error-'+field);   
      id = document.querySelector('#profile-values input[name="id"]').value;
      request = new XMLHttpRequest();
      request.open('PUT', '/api/users/' + id);
      request.setRequestHeader('Content-Type', 'application/json');
      switch(field){
        case 'username':  
          request.send(JSON.stringify({ username: newValue.value }));
          break
        case 'name':
          request.send(JSON.stringify({ name: newValue.value }));
          break
        case 'email':
          request.send(JSON.stringify({ email: newValue.value }));
          break;
        default:
          break;
      }
      request.onload = function() {
        if (request.status === 200 ){
          values.forEach((value) => {
            value.textContent = newValue.value;
          });
          paragraph.innerHTML = '';
          document.querySelector('#'+field+'-change .back-button').click();
        }
        else if (request.status === 400){
          let data = JSON.parse(request.responseText);  
          paragraph.innerHTML = data.error; 
        }
        newValue.value = '';
      }
    });
    backButton.addEventListener('click', () =>{
      newValue.value = '';
    })
  }
}

function updatePassword(saveButton, backButton, newValue, confirmValue) {
  if (saveButton !== null && backButton !== null && newValue !== null && confirmValue!== null) {   
    saveButton.addEventListener('click', () => { 
      id = document.querySelector('#profile-values input[name="id"]').value;
      if (newValue.value == confirmValue.value){
        request = new XMLHttpRequest();
        request.open('PUT', '/api/users/' + id);
        request.setRequestHeader('Content-Type', 'application/json');
        request.send(JSON.stringify({ password: newValue.value }));
      }
      else{
        alert('The Passwords do not match!');
      }
      newValue.value = '';
      confirmValue.value = '';
    });
    backButton.addEventListener('click', () =>{
      newValue.value = '';
      confirmValue.value = '';
    })
  }
}
  
saveButton = document.querySelector('#username-change #save-username');
backButton = document.querySelector('#username-change .back-button');
newValue = document.querySelector('#username-change input[name="username"]');
values = document.querySelectorAll('.value-username');
update(saveButton, backButton, newValue, values, 'username');

saveButton = document.querySelector('#name-change #save-name');
backButton = document.querySelector('#name-change .back-button');
newValue = document.querySelector('#name-change input[name="name"]');
values = document.querySelectorAll('.value-name');
update(saveButton, backButton, newValue, values, 'name');

saveButton = document.querySelector('#email-change #save-email');
backButton = document.querySelector('#email-change .back-button');
newValue = document.querySelector('#email-change input[name="email"]');
values = document.querySelectorAll('.value-email');
update(saveButton, backButton, newValue, values, 'email');

saveButton = document.querySelector('#password-change #save-password');
backButton = document.querySelector('#password-change .back-button');
newValue = document.querySelector('#password-change input[name="password"]');
confirmValue = document.querySelector('#password-change input[name="confirm-password"]');
updatePassword(saveButton, backButton, newValue, confirmValue);

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