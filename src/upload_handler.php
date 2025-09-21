<?php
session_start();
require_once __DIR__ . '/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header('Location: ../public/login.php?error=unauthorized');
    exit();
}

// Check if the form was submitted
if (isset($_POST['upload'])) {

    // --- 1. Get Form Data ---
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    if (empty($title)) {
        header('Location: ../public/dashboard.php?error=notitle');
        exit();
    }

    // --- 2. Handle File Upload ---
    if (isset($_FILES['documentFile']) && $_FILES['documentFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['documentFile'];

        // --- File Validation ---
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png'
        ];
        $max_size = 10 * 1024 * 1024; // 10 MB

        if (!in_array(mime_content_type($file['tmp_name']), $allowed_types)) {
            header('Location: ../public/dashboard.php?error=invalidtype');
            exit();
        }

        if ($file['size'] > $max_size) {
            header('Location: ../public/dashboard.php?error=toolarge');
            exit();
        }

        // --- Generate unique name and set path ---
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('doc_', true) . '.' . $file_extension;
        $upload_dir = __DIR__ . '/../uploads/';
        $file_path_server = $upload_dir . $file_name;

        // --- Move the file ---
        if (move_uploaded_file($file['tmp_name'], $file_path_server)) {
            // --- 3. Update Database ---
            $db = new Database();
            $conn = $db->connect();

            if ($conn) {
                try {
                    $conn->beginTransaction();

                    // Insert into documents table
                    $stmt_doc = $conn->prepare("INSERT INTO documents (user_id, title, description) VALUES (?, ?, ?)");
                    $stmt_doc->execute([$user_id, $title, $description]);
                    $document_id = $conn->lastInsertId();

                    // Insert into document_versions table
                    $stmt_ver = $conn->prepare("INSERT INTO document_versions (document_id, version_number, file_path) VALUES (?, ?, ?)");
                    $version_number = 1;
                    $file_path_db = 'uploads/' . $file_name; // Store relative path for web access
                    $stmt_ver->execute([$document_id, $version_number, $file_path_db]);

                    $conn->commit();
                    header('Location: ../public/dashboard.php?upload=success');
                    exit();

                } catch (PDOException $e) {
                    $conn->rollBack();
                    error_log("Database error during upload: " . $e->getMessage());
                    // Clean up the uploaded file if the DB operation fails
                    if (file_exists($file_path_server)) {
                        unlink($file_path_server);
                    }
                    header('Location: ../public/dashboard.php?error=dberror');
                    exit();
                }
            } else {
                header('Location: ../public/dashboard.php?error=dbconnection');
                exit();
            }
        } else {
            header('Location: ../public/dashboard.php?error=movefailed');
            exit();
        }
    } else {
        // Handle file upload errors
        $upload_error = isset($_FILES['documentFile']['error']) ? $_FILES['documentFile']['error'] : 'unknown';
        header('Location: ../public/dashboard.php?error=uploaderror&code=' . $upload_error);
        exit();
    }
} else {
    // Redirect if accessed directly or without correct form submission
    header('Location: ../public/dashboard.php');
    exit();
}
?>
