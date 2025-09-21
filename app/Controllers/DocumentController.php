<?php

namespace App\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;

class DocumentController extends Controller
{
    /**
     * Store a new document.
     */
    public function store()
    {
        // --- 1. Validation ---
        if (empty($_POST['title']) || !isset($_FILES['documentFile']) || $_FILES['documentFile']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /dashboard?error=invalid_input');
            exit();
        }

        // --- 2. File Handling ---
        $file = $_FILES['documentFile'];

        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png'
        ];
        $max_size = 10 * 1024 * 1024; // 10 MB

        if (!in_array(mime_content_type($file['tmp_name']), $allowed_types) || $file['size'] > $max_size) {
            header('Location: /dashboard?error=invalid_file');
            exit();
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('doc_', true) . '.' . $file_extension;
        $upload_dir = __DIR__ . '/../../uploads/';
        $file_path_server = $upload_dir . $file_name;
        $file_path_db = 'uploads/' . $file_name;

        if (!move_uploaded_file($file['tmp_name'], $file_path_server)) {
            header('Location: /dashboard?error=move_failed');
            exit();
        }

        // --- 3. Database Interaction ---
        $db = (new \App\Core\Database())->connect();

        try {
            $db->beginTransaction();

            $docModel = new Document();
            $versionModel = new DocumentVersion();

            // Create the main document record
            $documentId = $docModel->create([
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description'] ?? '')
            ]);

            if (!$documentId) {
                throw new \Exception("Failed to create document record.");
            }

            // Create the first version record
            $versionSuccess = $versionModel->create([
                'document_id' => $documentId,
                'version_number' => 1,
                'file_path' => $file_path_db
            ]);

            if (!$versionSuccess) {
                throw new \Exception("Failed to create document version record.");
            }

            $db->commit();
            header('Location: /dashboard?upload=success');
            exit();

        } catch (\Exception $e) {
            $db->rollBack();

            // Clean up the orphaned file
            if (file_exists($file_path_server)) {
                unlink($file_path_server);
            }

            error_log("Document upload failed: " . $e->getMessage());
            header('Location: /dashboard?error=dberror');
            exit();
        }
    }

    /**
     * Securely serve a file for download/viewing.
     */
    public function download()
    {
        if (empty($_GET['file'])) {
            http_response_code(400);
            echo "Bad Request: No file specified.";
            exit();
        }

        $filePath = $_GET['file'];

        // --- Security Check ---
        // 1. Sanitize the file path to prevent directory traversal.
        // realpath() resolves all symlinks and '..' and returns the canonicalized absolute path.
        // We ensure the requested path starts with our secure uploads directory path.
        $basePath = realpath(__DIR__ . '/../../uploads');
        $realFilePath = realpath(__DIR__ . '/../../' . $filePath);

        if ($realFilePath === false || strpos($realFilePath, $basePath) !== 0) {
            http_response_code(403);
            echo "Forbidden: Access denied.";
            exit();
        }

        // 2. Check if file exists
        if (!file_exists($realFilePath)) {
            http_response_code(404);
            echo "Not Found: The requested file does not exist.";
            exit();
        }

        // For now, we don't check for ownership. In a real app, you would verify
        // that $_SESSION['user_id'] has permission to access this document.

        // --- Serve the file ---
        header('Content-Description: File Transfer');
        header('Content-Type: ' . mime_content_type($realFilePath));
        header('Content-Disposition: inline; filename="' . basename($realFilePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($realFilePath));

        // Clear output buffer
        flush();

        // Read the file and write it to the output buffer
        readfile($realFilePath);
        exit();
    }
}
