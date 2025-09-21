<?php

namespace App\Core;

// The path needs to go up two levels from app/Core and then into config.
require_once __DIR__ . '/../../config/app.php';

class Database
{
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            // In a real app, this should be logged and a generic error shown.
            die('Connection Error: ' . $e->getMessage());
        }

        return $this->conn;
    }
}
