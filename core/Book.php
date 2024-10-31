<?php
class Book
{
    public $conn;
    public $table = 'books';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getBooks($id = null)
    {
        if ($id) {
            $stmt = $this->conn->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ?');
            $stmt->execute([$id]);
            // var_dump($stmt);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($book) {
                return $book;
            } else {
                http_response_code(404);
                return ['error' => 'book not found'];
            }
        } else {
            $stmt = $this->conn->query("SELECT * FROM $this->table");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public function create($title, $author, $year)
    {
        if (!is_numeric($year)) {
            http_response_code(400);
            return ['error' => 'Year must be a number'];
        }
        $stmt = $this->conn->prepare("INSERT INTO $this->table (title, author, year) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $author, $year])) {
            http_response_code(201);
            return ['id' => $this->conn->lastInsertId(), 'title' => $title, 'author' => $author, 'year' => $year];
        } else {
            http_response_code(404);
            return ['error' => 'Unable to create book.'];
        }
    }
    public function update($id, $title = null, $author = null, $year = null)
    {
        $set = [];
        $params = [];

        if (!is_numeric($id)) {
            http_response_code(400);
            return ['error' => 'ID must be a number'];
        }

        if ($year && !is_numeric($year)) {
            http_response_code(400);
            return ['error' => 'Year must be a number'];
        }

        if ($title) {
            $set[] = "title = ?";
            $params[] = $title;
        }
        if ($author) {
            $set[] = "author = ?";
            $params[] = $author;
        }
        if ($year) {
            $set[] = "year = ?";
            $params[] = $year;
        }

        if (empty($set)) {
            http_response_code(400);
            return ['error' => 'No fields to update'];
        }

        $query = "UPDATE $this->table SET " . implode(', ', $set) . " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->conn->prepare($query);
        if ($stmt->execute($params)) {
            if ($stmt->rowCount()) {
                http_response_code(200);
                return ['message' => 'Book updated successfully.'];
            } else {
                http_response_code(404);
                return ['error' => 'Book not found.'];
            }
        } else {
            http_response_code(400);
            return ['error' => 'Unable to update book.'];
        }
    }

    public function delete($id) {
        if (!is_numeric($id)) {
            http_response_code(400);
            return ['error' => 'ID must be a number'];
        }

        $stmt = $this->conn->prepare('DELETE FROM ' . $this->table . ' WHERE id = ?');
        if($stmt->execute([$id])){
            if($stmt->rowCount()){
                http_response_code(204);
            }else{
                http_response_code(404);
                return ['message' => 'book not found'];
            }
        }else{
            http_response_code(400);
            return ['error' => 'Unable to delete book.'];
        }
    }
}
