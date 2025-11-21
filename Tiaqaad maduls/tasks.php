
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = ""; // password haddii uu jiro
$dbname = "todo_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $row['completed'] = (bool)$row['completed'];
            $tasks[] = $row;
        }
        echo json_encode($tasks);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $text = $conn->real_escape_string($data['text']);
        $completed = $data['completed'] ? 1 : 0;
        $created_at = date('Y-m-d H:i:s');

        $sql = "INSERT INTO tasks (id, text, completed, created_at) VALUES ($id, '$text', $completed, '$created_at')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $completed = $data['completed'] ? 1 : 0;
        $sql = "UPDATE tasks SET completed=$completed WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $sql = "DELETE FROM tasks WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => $conn->error]);
        }
        break;
}
$conn->close();
?>
``