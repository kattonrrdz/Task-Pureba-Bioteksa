<?php
include_once('db.php');
include_once('Task.php');

// Endpoint para obtener todas las tareas
function getTasks() {
    $db = new Database();
    $conn = $db->getConnection();

    $result = $conn->query('SELECT * FROM tasks');
    $taskList = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $task = new Task($row['id'], $row['title'], $row['description'], $row['completed'], $row['user_id'], $row['created_at']);
            $taskList[] = json_decode($task->toJson(), true);
        }
    }

    return json_encode($taskList);
}

// Endpoint para obtener los detalles de una tarea por ID
function getTaskDetails($id) {
    $task = new Task($id, null, null, null, null, null);
    $taskDetails = $task->loadFromDatabase();
    if ($taskDetails) {
        return json_encode($taskDetails);
    } else {
        http_response_code(404);
        return json_encode(['error' => 'Task not found']);
    }
}

// Endpoint para agregar una tarea
function addTask($title, $description, $user_id) {
    $task = new Task(null, $title, $description, false, $user_id, null);
    return $task->saveToDatabase();
}

// Endpoint para eliminar una tarea
function deleteTask($id) {
    $task = new Task($id, null, null, false, null, null);
    return $task->deleteFromDatabase();
}

// Endpoint para actualizar una tarea
function updateTask($id, $title, $description, $completed) {
    $task = new Task($id, $title, $description, $completed, null, null);
    return $task->updateInDatabase();
}

// Manejo de las solicitudes GET POST PATCH
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if ($id !== null) {
        echo getTaskDetails($id);
    } else {
        echo getTasks();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['title']) && isset($data['description']) && isset($data['user_id'])) {
        echo addTask($data['title'], $data['description'], $data['user_id']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if ($id !== null) {
        echo deleteTask($id);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if ($id !== null) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['title']) && isset($data['description']) && isset($data['completed'])) {
            echo updateTask($id, $data['title'], $data['description'], $data['completed']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Request']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
