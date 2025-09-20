<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        header('Location: ../public/register.php?error=emptyfields');
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $database = new Database();
    $conn = $database->connect();

    if ($conn) {
        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                header('Location: ../public/register.php?error=userexists');
                exit();
            }

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                header('Location: ../public/login.php?registration=success');
                exit();
            } else {
                header('Location: ../public/register.php?error=sqlerror');
                exit();
            }
        } catch (PDOException $e) {
            // Log error message instead of showing to user
            error_log($e->getMessage());
            header('Location: ../public/register.php?error=dberror');
            exit();
        }
    } else {
        header('Location: ../public/register.php?error=dbconnection');
        exit();
    }
} else {
    header('Location: ../public/register.php');
    exit();
}
?>
