function update(saveButton, backButton, newValue, values, field) {
  if (saveButton !== null && backButton !== null && newValue !== null && values !== null && field !== null) { 
    saveButton.addEventListener('click', () => {    
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
          console.log('working');
          values.forEach((value) => {
            console.log('in');
            value.textContent = newValue.value;
          })
        }
        else if (request.status === 400){

        }
      }
      newValue.value = '';
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
updatePassword(saveButton, backButton, newValue, confirmValue, values);