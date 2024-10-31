<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Database
{
    private $host;
    private $username;
    private $password;
    private $db_name;
    public $conn;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable('/opt/homebrew/var/www/api_project_2');
        $dotenv->load();

        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->db_name = $_ENV['DB_NAME'];
    }

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection error: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
