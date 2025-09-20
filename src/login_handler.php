<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header('Location: ../public/login.php?error=emptyfields');
        exit();
    }

    $database = new Database();
    $conn = $database->connect();

    if ($conn) {
        try {
            $stmt = $conn->prepare("SELECT id, username, password, role_id FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    // Password is correct, start session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];
                    header('Location: ../public/dashboard.php');
                    exit();
                } else {
                    // Invalid password
                    header('Location: ../public/login.php?error=invalidcred');
                    exit();
                }
            } else {
                // No user found
                header('Location: ../public/login.php?error=invalidcred');
                exit();
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            header('Location: ../public/login.php?error=dberror');
            exit();
        }
    } else {
        header('Location: ../public/login.php?error=dbconnection');
        exit();
    }
} else {
    header('Location: ../public/login.php');
    exit();
}
?>
