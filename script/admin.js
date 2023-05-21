/*function changeProfilePopUp(buttons, menu, back) {
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
  }*/
  
function updateDepartment() {
    const button = document.querySelector('#Add-Department .enter-button');
    console.log(button);
    if (true) { 
        button.addEventListener('click', () => {
            department = document.querySelector('#Add-Department input[name="department"]')
            console.log(department);
            request = new XMLHttpRequest();
            request.open('POST', '/api/departments/')
            request.setRequestHeader('Content-Type', 'application/json');
            request.send(JSON.stringify({ name: department.value }));
            department.value='';
        });
    };
}



/*function updateRole() {
    const button = document.querySelector('#update-role #enter-role');
    const selects = document.querySelectorAll('#update-role select')
    if (true) { 
        button.addEventListener('click', () => {
            selects.forEach(select => {
                value = select.options[select.selectedIndex].value;
                request = new XMLHttpRequest();
                id = document.querySelector("#update-role input[id='user-id'][name='"+select.id+"']").value;
                admin = document.querySelector("#update-role input[id='user-admin'][name='"+select.id+"']");
                agent = document.querySelector("#update-role input[id='user-agent'][name='"+select.id+"']");
                paragraph = document.querySelector("#update-role p[id='"+select.id+"']");
                flag = false;
                switch(value){
                    case 'Client':
                        if (agent.value){
                            request.open('DELETE', '/api/agents/' + id);
                            admin.value = '';
                            agent.value = '';
                            flag = true;
                        }
                        paragraph.innerHTML = select.id + ' - Client';
                        break
                    case 'Agent':
                        if (admin.value){
                            request.open('DELETE', '/api/admins/' + id)
                            admin.value = '';
                            flag = true;
                        }
                        else if (!agent.value){
                            request.open('POST', '/api/agents/' + id)
                            agent.value = '1';
                            flag = true;
                        }
                        paragraph.innerHTML = select.id + ' - Agent';
                        break;
                    case 'Admin':
                        if (!admin.value){
                            request.open('POST', '/api/admins/' + id)
                            admin.value = '1';
                            agent.value = '1'
                            flag = true;
                        }
                        paragraph.innerHTML = select.id + ' - Admin';
                        break;
                    default:
                        break;
                }
                if(flag)
                    request.send();
            });
        });
    }
}
  
updateRole();*/
updateDepartment()

  /*saveButton = document.querySelector('#username-change #save-username');
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
  changeProfilePopUp(password_button, password_popUp, password_back);*/