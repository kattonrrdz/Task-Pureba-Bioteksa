function addTask() {
    const title = document.getElementById('taskTitle').value;
    const description = document.getElementById('taskDescription').value;

    if (title && description) {
        const data = {
            title: title,
            description: description,
            user_id: 1 // para no crear un login de almacenamiento de usuario agregare esta constante de usuario
        };

        fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(task => {
            displayTask(task);
        })
        .catch(error => console.error('Error:', error));
    } else {
        alert('Porfavor agrege un titulo a la tarea.');
    }
}
// voy a usar este script para comunicarme con el api y crear unos botones para probarlo en una interface
function displayTask(task) {
    const taskList = document.getElementById('taskList');
    const taskItem = document.createElement('li');
    taskItem.classList.add('task-item');

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.checked = task.completed;
    checkbox.addEventListener('change', () => toggleTaskCompletion(task.id, checkbox.checked));

    const label = document.createElement('label');
    label.appendChild(document.createTextNode(`${task.title} - ${task.description}`));

    const editButton = document.createElement('button');
    editButton.innerText = 'Edit';
    editButton.addEventListener('click', () => editTask(task));

    const deleteButton = document.createElement('button');
    deleteButton.innerText = 'Delete';
    deleteButton.addEventListener('click', () => deleteTask(task.id));

    const detailsButton = document.createElement('button');
    detailsButton.innerText = 'Details';
    detailsButton.addEventListener('click', () => showTaskDetails(task));

    taskItem.appendChild(checkbox);
    taskItem.appendChild(label);
    taskItem.appendChild(editButton);
    taskItem.appendChild(deleteButton);
    taskItem.appendChild(detailsButton);

    taskList.appendChild(taskItem);
}

function toggleTaskCompletion(id, completed) {
    const data = {
        completed: completed
    };

    fetch(`api.php?id=${id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(task => {
        // La actualizacion de la tarea empieza aqui
    })
    .catch(error => console.error('Error:', error));
}

function editTask(task) {
    const editTaskForm = document.getElementById('editTaskForm');
    const editTaskTitle = document.getElementById('editTaskTitle');
    const editTaskDescription = document.getElementById('editTaskDescription');
    const editTaskCompleted = document.getElementById('editTaskCompleted');

    editTaskTitle.value = task.title;
    editTaskDescription.value = task.description;
    editTaskCompleted.checked = task.completed;

    // Con esto actualzamos la tarea en la base de datos
    editTaskForm.dataset.taskId = task.id;
    editTaskForm.style.display = 'block';
}
//Funcion para mostrar los detalles 

function showTaskDetails(task) {
    alert(`Task Details:\nTitle: ${task.title}\nDescription: ${task.description}\nCompleted: ${task.completed}`);
}

function updateTaskUI(task) {
    const taskList = document.getElementById('taskList');
    const taskItems = taskList.getElementsByClassName('task-item');

    for (let i = 0; i < taskItems.length; i++) {
        const taskId = taskItems[i].getAttribute('data-task-id');

        if (taskId === task.id) {
            const label = taskItems[i].getElementsByTagName('label')[0];
            label.innerText = `${task.title} - ${task.description}`;
            break;
        }
    }
}

function saveEditedTask() {
    const editTaskForm = document.getElementById('editTaskForm');
    const editedTitle = document.getElementById('editTaskTitle').value;
    const editedDescription = document.getElementById('editTaskDescription').value;
    const editedCompleted = document.getElementById('editTaskCompleted').checked;
    const taskId = editTaskForm.dataset.taskId;

    if (editedTitle && editedDescription && taskId) {
        const data = {
            title: editedTitle,
            description: editedDescription,
            completed: editedCompleted
        };

        fetch(`api.php?id=${taskId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(task => {
            
        })
        .catch(error => console.error('Error:', error));

        editTaskForm.dataset.taskId = '';  // Este codigo hace un Reset al Id de la tarea
        editTaskForm.style.display = 'none';
    } else {
        alert('Porfavor agrege un titulo a la descripcion de la tarea.');
    }
}

function deleteTask(id) {
    fetch(`api.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            removeTaskFromUI(id); // Elimina la tarea de la interfaz
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function removeTaskFromUI(id) {
    const taskList = document.getElementById('taskList');
    const taskItems = taskList.getElementsByClassName('task-item');

    for (let i = 0; i < taskItems.length; i++) {
        const taskId = taskItems[i].getAttribute('data-task-id');

        if (taskId === id) {
            taskItems[i].remove();
            break;
        }
    }
}
