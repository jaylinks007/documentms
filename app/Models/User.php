<?php

namespace App\Models;

class User
{
    protected $db;

    public function __construct()
    {
        // This assumes the Database class from core/Database.php is available.
        // A more advanced solution might use a dependency injection container.
        $this->db = new \App\Core\Database();
    }

    /**
     * Create a new user in the database.
     *
     * @param array $data The user's data.
     * @return bool True on success, false on failure.
     */
    public function create(array $data)
    {
        $conn = $this->db->connect();

        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password, organisation_name, department, project_name)
             VALUES (:username, :email, :password, :organisation_name, :department, :project_name)"
        );

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':organisation_name', $data['organisation_name']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':project_name', $data['project_name']);

        return $stmt->execute();
    }

    /**
     * Find a user by their username.
     *
     * @param string $username The username to search for.
     * @return mixed The user data as an associative array, or false if not found.
     */
    public function findByUsername($username)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
