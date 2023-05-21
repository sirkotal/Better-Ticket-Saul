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
    select_add = document.querySelectorAll('#add-department select');
    select_remove = document.querySelectorAll('#remove-department select');
    added_selects = [];
    removed_selects = []; 
    if (true) { 
        button_add.addEventListener('click', () => {
            optionsToRemove = [];
            select_add = [...select_add, ...removed_selects];
            select_add.forEach(select => {
                option = select.options[select.selectedIndex];
                id = document.querySelector("#add-department input[class='user-id'][name='"+select.id+"']").value;
                if (option.value != 'none'){
                    optionsToRemove.push({select,option, id});
                    request = new XMLHttpRequest();
                    request.open('PUT', '/api/agents/'+id)
                    request.setRequestHeader('Content-Type', 'application/json');
                    request.send(JSON.stringify({ action: 'add', departmentId: option.value}));
                }
            })
            optionsToRemove.forEach(({select, option, id}) => {
                option.remove();
                flag = false;
                if (select.options.length<=1){
                    select.style.display = "none";
                    select.style.pointerEvents = "none";
                    title = document.querySelector('#add-department p[id="'+select.id+'"]');
                    title.style.display = 'none';
                }
                select1 = document.querySelector('#remove-department select[id="'+select.id+'"]');
                if (select1 == null){
                    flag = true;
                    select1 = document.createElement('select');
                    select1.name = 'Departments';
                    select1.id = select.id;
                }
                select1.style.display = "block";
                select1.style.pointerEvents = "auto";
                /*ptionDefault = document.createElement('option');
                optionDefault.value = "none"
                optionDefault.textContent = '--Default--'
                select1.appendChild(optionDefault)*/    
                selected = document.createElement('option');
                selected.value = option.value;
                selected.textContent = option.textContent;
                select1.appendChild(selected);
                title1 = document.querySelector('#remove-department p[id="'+select.id+'"]');
                if (title1 == null){
                    title1 = document.createElement('p');
                    title1.className = 'title';
                    title1.id = select.id;
                    title1.innerHTML = select.id; 
                }
                title1.style.display = 'block';
                idinput = document.createElement('input');
                idinput.className = 'user-id';
                idinput.type = 'hidden';
                idinput.name = select.id;
                idinput.value = id;
                div = document.querySelector('#remove-department')
                buttons = document.querySelector('#remove-department .change-buttons')
                div.insertBefore(select1, buttons);
                div.insertBefore(idinput, select1);
                div.insertBefore(title1, idinput);
            });
            optionsToRemove.length = 0;
        })
        button_remove.addEventListener('click', async () => {
            optionsToAdd = [];
            select_remove = [...select_remove, ...added_selects];
            select_remove.forEach(select => {
                option = select.options[select.selectedIndex];
                id = document.querySelector("#remove-department input[class='user-id'][name='"+select.id+"']").value;
                if (option.value != 'none'){
                    optionsToAdd.push({select,option, id});
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
            optionsToAdd.forEach(({select,option, id}) => {
                option.remove();
                flag=false;
                console.log(select.options.length);
                if (select.options.length<=1){
                    select.style.display = "none";
                    select.style.pointerEvents = "none";
                    title = document.querySelector('#remove-department p[id="'+select.id+'"]');
                    title.style.display = 'none';
                }
                select2 = document.querySelector('#add-department select[id="'+select.id+'"]');
                if (select2 == null){
                    flag=true;
                    select2 = document.createElement('select');
                    select2.name = 'Departments'
                    select2.id = select.id;
                }
                select2.style.display = "block";
                select2.style.pointerEvents = "auto";
                selected = document.createElement('option');
                selected.value = option.value;
                selected.textContent = option.textContent;
                select2.appendChild(selected);
                title2 = document.querySelector('#add-department p[id="'+select.id+'"]');
                if (title2 == null){
                    title2 = document.createElement('p');
                    title2.className = 'title';
                    title2.id = select.id;
                    title2.innerHTML = select.id; 
                }
                title2.style.display = 'block';
                idinput2 = document.createElement('input');
                idinput2.className = 'user-id';
                idinput2.type = 'hidden';
                idinput2.name = select.id;
                idinput2.value = id;
                div = document.querySelector('#add-department')
                buttons = document.querySelector('#add-department .change-buttons')
                div.insertBefore(select2, buttons);
                div.insertBefore(idinput2, select2);
                div.insertBefore(title2, idinput2);
            });

        })
    }
}



function updateDepartment() {
    const button = document.querySelector('#New-Department .enter-button');
    if (true) { 
        button.addEventListener('click', () => {
            department = document.querySelector('#New-Department input[name="department"]')
            id = document.querySelector('#New-Department input[id="department-id"]');
            request = new XMLHttpRequest();
            request.open('POST', '/api/departments/')
            request.setRequestHeader('Content-Type', 'application/json');
            request.send(JSON.stringify({ name: department.value }));
            selects = document.querySelectorAll('#add-department select');
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

function updateStatus() {
    const button = document.querySelector('#Add-Status .enter-button');
    if (true) { 
        button.addEventListener('click', () => {
            stat = document.querySelector('#Add-Status input[name="status"]')
            color = document.querySelector('#Add-Status input[name="status-color"]')
            request = new XMLHttpRequest();
            request.open('POST', '/api/status/')
            request.setRequestHeader('Content-Type', 'application/json');
            request.send(JSON.stringify({ status: stat.value, color: color.value }));
            request.onload = function() {
                if (request.status === 201 ){
                    document.querySelector('#Add-Status .back-button').click();
                    alert('Status added successfully!');
                    stat.value = '';
                }
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
updateStatus();

const admin_buttons = document.querySelectorAll('#admin-options button');
const admin_menus = document.querySelectorAll('#admin-forms');
const back_button = document.querySelectorAll('.back-button');
PopUp(admin_buttons, admin_menus, back_button)

