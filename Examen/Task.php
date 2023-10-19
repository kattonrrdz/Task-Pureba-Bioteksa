<?php
include_once('db.php');
//Aqui pongo la clase que viene en el documento tarea Task
class Task {
    public $id;
    public $title;
    public $description;
    public $completed;
    public $user_id;
    public $created_at;

    public function __construct($id, $title, $description, $completed, $user_id, $created_at) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->completed = $completed;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
    }

    public function toJson() {
        return json_encode([
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at
        ]);
    }
//agrege esta funcion para hacer pruebas con una base de datos y almacenar los public
    public function saveToDatabase() {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare('INSERT INTO tasks (title, description, completed, user_id, created_at) VALUES (?, ?, ?, ?, ?)');
        $completed = 0;
        $created_at = date('Y-m-d H:i:s');
        $stmt->bind_param('ssiss', $this->title, $this->description, $completed, $this->user_id, $created_at);

        if ($stmt->execute()) {
            $this->id = $conn->insert_id;
            return $this->toJson();
        } else {
            http_response_code(500);
            return json_encode(['error' => 'Failed to add task to database']);
        }
    }
//Funcion para actualizar la base de datos
    public function updateInDatabase() {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare('UPDATE tasks SET title = ?, description = ?, completed = ? WHERE id = ?');
        $stmt->bind_param('ssii', $this->title, $this->description, $this->completed, $this->id);

        if ($stmt->execute()) {
            return $this->toJson();
        } else {
            http_response_code(500);
            return json_encode(['error' => 'Failed to update task in database']);
        }
    }
//Funcion para eliminar la tarea de la base de datos
    public function deleteFromDatabase() {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->bind_param('i', $this->id);

        if ($stmt->execute()) {
            return json_encode(['message' => 'Task deleted successfully']);
        } else {
            http_response_code(500);
            return json_encode(['error' => 'Failed to delete task from database']);
        }
    }
    //Funcion para ver los detalles de la tarea
    public function loadFromDatabase() {
        $db = new Database();
        $conn = $db->getConnection();

        $query = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $this->title = $row['title'];
                $this->description = $row['description'];
                $this->completed = $row['completed'];
                $this->user_id = $row['user_id'];
                $this->created_at = $row['created_at'];
                return true;
            }
        }
        return false;
    }
}

?>
