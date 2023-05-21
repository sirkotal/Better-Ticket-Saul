function PopUp(buttons) {
    if (buttons!== null && menu !== null /*&& back !== null*/) {   
      const body = document.querySelector('#admin-options');
      const header = document.querySelector('header');
      buttons.forEach((button) => {
        back = document.querySelector('div[class="admin-forms '+button.className+'"] .back-button');
        if (button !== null && header!==null && body !== null) {
          button.addEventListener('click', () => {
            menu = document.querySelector('div[class="admin-forms '+button.className+'"]');
            menu.classList.add('expanded');
            body.style.pointerEvents = 'none';
            header.style.pointerEvents = 'none';
            body.style.filter = 'grayscale(1)';
            header.style.filter = 'grayscale(1)';   
          });
          back.addEventListener('click', () => {
            menu.classList.remove('expanded');
            body.style.pointerEvents = 'auto';
            header.style.pointerEvents = 'auto';
            body.style.filter = 'grayscale(0)';
            header.style.filter = 'grayscale(0)';
            })
        }
      });
    }
  }

  
function assignAgents() {
    const button_add = document.querySelector('#add-department .enter-button');
    const button_remove = document.querySelector('#remove-department .enter-button');
    const select_add = document.querySelectorAll('#add-department select')
    const select_remove = document.querySelectorAll('#remove-department select')
    if (true) { 
        button_add.addEventListener('click', () => {
            const optionsToRemove = [];
            select_add.forEach(select => {
                option = select.options[select.selectedIndex];
                id = document.querySelector("#add-department input[class='user-id'][name='"+select.id+"']").value;
                if (option.value != 'none'){
                    optionsToRemove.push(option);
                    request = new XMLHttpRequest();
                    request.open('PUT', '/api/agents/'+id)
                    request.setRequestHeader('Content-Type', 'application/json');
                    request.send(JSON.stringify({ action: 'add', departmentId: option.value}));
                }
            });
            optionsToRemove.forEach(option => {
                option.remove();
            });
        });
        button_remove.addEventListener('click', async () => {
            const optionsToRemove = [];
            select_remove.forEach(select => {
                option = select.options[select.selectedIndex];
                id = document.querySelector("#remove-department input[class='user-id'][name='"+select.id+"']").value;
                if (option.value != 'none'){
                    optionsToRemove.push(option);
                    request = new XMLHttpRequest();
                    request.open('PUT', '/api/agents/'+id)
                    request.setRequestHeader('Content-Type', 'application/json');
                    request.send(JSON.stringify({ action: 'remove', departmentId: option.value}));
                    remove = option;
                    request.onload = function() {
                        if (request.status === 200 ){
                            remove.remove();
                        }
                    }
                }
            });
            optionsToRemove.forEach(option => {
                option.remove();
            });
        })
    };
}


function updateDepartment() {
    const button = document.querySelector('#New-Department .enter-button');
    console.log(button);
    if (true) { 
        button.addEventListener('click', () => {
            department = document.querySelector('#New-Department input[name="department"]')
            id = document.querySelector('#New-Department input[id="department-id"]');
            console.log(department);
            request = new XMLHttpRequest();
            request.open('POST', '/api/departments/')
            request.setRequestHeader('Content-Type', 'application/json');
            request.send(JSON.stringify({ name: department.value }));
            selects = document.querySelectorAll('#add-department select');
            console.log(selects);
            request.onload = function() {
                paragraph = document.querySelector('#New-Department .error'); 
                if (request.status === 201 ){
                    document.querySelector('#New-Department .back-button').click();
                    alert('Department added successfully!');
                    id.value+=1;
                    paragraph.innerHTML = ''; 
                    selects.forEach(select => {
                        option = document.createElement('option');
                        option.value = id.value;
                        option.textContent = department.value;
                        select.appendChild(option);
                    });
                }
                else if (request.status === 400){
                    let data = JSON.parse(request.responseText); 
                    paragraph.innerHTML = data.error; 
                }
                department.value='';
            }
        });
    };
}



function updateRole() {
    const button = document.querySelector('#update-role #enter-role');
    const selects = document.querySelectorAll('#update-role select');
    if (true) { 
        button.addEventListener('click', () => {
            selects.forEach(select => {
                console.log('here');
                value = select.options[select.selectedIndex].value;
                request = new XMLHttpRequest();
                id = document.querySelector("#update-role input[class='user-id'][name='"+select.id+"']").value;
                admin = document.querySelector("#update-role input[id='user-admin'][name='"+select.id+"']");
                agent = document.querySelector("#update-role input[id='user-agent'][name='"+select.id+"']");
                paragraph = document.querySelectorAll("p[id='"+select.id+"']");
                flag = false;
                switch(value){
                    case 'Client':
                        if (agent.value){
                            request.open('DELETE', '/api/agents/' + id);
                            admin.value = '';
                            agent.value = '';
                            flag = true;
                        }
                        paragraph.forEach(p => {
                            p.innerHTML = select.id + ' - Client';
                        });
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
                        paragraph.forEach(p => {
                            p.innerHTML = select.id + ' - Agent';
                        });
                        break;
                    case 'Admin':
                        if (!admin.value){
                            request.open('POST', '/api/admins/' + id)
                            admin.value = '1';
                            agent.value = '1'
                            flag = true;
                        }
                        paragraph.forEach(p => {
                            p.innerHTML = select.id + ' - Admin';
                        });
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

updateDepartment()
updateRole();
assignAgents();

const admin_buttons = document.querySelectorAll('#admin-options button');
const admin_menus = document.querySelectorAll('#admin-forms');
const back_button = document.querySelectorAll('.back-button');
PopUp(admin_buttons, admin_menus, back_button)

