<?php
header('Content-Type: application/json');
include_once('../core/Database.php');
include_once('../core/Book.php');
// require '/opt/homebrew/var/www/api_project_2/core/Database.php';
// require '/opt/homebrew/var/www/api_project_2/core/Book.php';

$database = new Database();
$db = $database->connect();
$book = new Book($db);


$method = $_SERVER['REQUEST_METHOD'];
$bookId = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($bookId) {
            echo json_encode($book->getBooks($bookId));
        } else {
            echo json_encode($book->getBooks());
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($book->create($data['title'], $data['author'], $data['year']));
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required for update']);
            break;
        }
        echo json_encode($book->update($data['id'], $data['title'], $data['author'], $data['year']));
        break;

    case 'DELETE':
        if ($bookId) {
            echo json_encode($book->delete($bookId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Book ID is required for DELETE']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
