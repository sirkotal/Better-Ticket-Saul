function changeProfilePopUp(buttons, menu, back) {
  if (buttons && menu && back) {   
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
  if (saveButton && backButton && newValue && values && field) { 
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
          alert(field+' updated with success!')
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
  if (saveButton && backButton && newValue && confirmValue) {   
    saveButton.addEventListener('click', () => { 
      id = document.querySelector('#profile-values input[name="id"]').value;
      if (newValue.value == confirmValue.value){
        request = new XMLHttpRequest();
        request.open('PUT', '/api/users/' + id);
        request.setRequestHeader('Content-Type', 'application/json');
        request.send(JSON.stringify({ password: newValue.value }));
        request.onload = function() {
          if (request.status === 200 ){
            values.forEach((value) => {
              value.textContent = newValue.value;
            });
            paragraph.innerHTML = '';
            document.querySelector('#password-change .back-button').click();
            alert('Password updated successfully!');
          }
          else if (request.status === 400){
            let data = JSON.parse(request.responseText);  
            paragraph.innerHTML = data.error; 
          }
          newValue.value = '';
          confirmValue.value = '';
        }
      }
      else{
        paragraph = document.querySelector('.profile-change #error-password');   
        paragraph.innerHTML = 'The Passwords do not match!';
      }
    });
    backButton.addEventListener('click', () =>{
      newValue.value = '';
      confirmValue.value = '';
    })
  }
}
  
function initializeFieldChange(field, saveButtonId, backButtonClass, newValueName, valuesClass) {
  const saveButton = document.querySelector('#' + field + '-change #' + saveButtonId);
  const backButton = document.querySelector('#' + field + '-change .' + backButtonClass);
  const newValue = document.querySelector('#' + field + '-change input[name="' + newValueName + '"]');
  const values = document.querySelectorAll('.' + valuesClass);
  update(saveButton, backButton, newValue, values, field);
}

function initializePasswordChange(saveButtonId, backButtonClass, newValueName, confirmValueName) {
  const saveButton = document.querySelector('#password-change #' + saveButtonId);
  const backButton = document.querySelector('#password-change .' + backButtonClass);
  const newValue = document.querySelector('#password-change input[name="' + newValueName + '"]');
  const confirmValue = document.querySelector('#password-change input[name="' + confirmValueName + '"]');
  updatePassword(saveButton, backButton, newValue, confirmValue);
}

initializeFieldChange('username', 'save-username', 'back-button', 'username', 'value-username');
initializeFieldChange('name', 'save-name', 'back-button', 'name', 'value-name');
initializeFieldChange('email', 'save-email', 'back-button', 'email', 'value-email');
initializePasswordChange('save-password', 'back-button', 'password', 'confirm-password');

const usernameButton = document.querySelectorAll('.username-button');
const usernamePopUp = document.querySelector('#username-change');
const usernameBack = document.querySelector('#username-change .back-button');
changeProfilePopUp(usernameButton, usernamePopUp, usernameBack);

const nameButton = document.querySelectorAll('.name-button');
const namePopUp = document.querySelector('#name-change');
const nameBack = document.querySelector('#name-change .back-button');
changeProfilePopUp(nameButton, namePopUp, nameBack);

const emailButton = document.querySelectorAll('.email-button');
const emailPopUp = document.querySelector('#email-change');
const emailBack = document.querySelector('#email-change .back-button');
changeProfilePopUp(emailButton, emailPopUp, emailBack);

const passwordButton = document.querySelectorAll('.password-button');
const passwordPopUp = document.querySelector('#password-change');
const passwordBack = document.querySelector('#password-change .back-button');
changeProfilePopUp(passwordButton, passwordPopUp, passwordBack);