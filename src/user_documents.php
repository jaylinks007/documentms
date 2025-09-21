<?php
// This script should not be accessed directly.
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    exit('No direct script access allowed');
}

// Ensure database class is available, but don't re-declare if already included.
if (!class_exists('Database')) {
    require_once __DIR__ . '/database.php';
}

function get_user_documents($user_id) {
    $database = new Database();
    $conn = $database->connect();
    $documents = [];

    if ($conn) {
        try {
            // For now, we fetch all documents and their latest version.
            // When we implement multiple versions, this query will need to be more complex
            // to select only the latest version of each document.
            $sql = "SELECT
                        d.id,
                        d.title,
                        d.description,
                        dv.file_path,
                        dv.created_at AS upload_date
                    FROM documents d
                    INNER JOIN document_versions dv ON d.id = dv.document_id
                    WHERE d.user_id = ?
                    -- This ensures we get the latest version for each document
                    AND dv.version_number = (
                        SELECT MAX(version_number)
                        FROM document_versions
                        WHERE document_id = d.id
                    )
                    ORDER BY d.created_at DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id]);
            $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database error in get_user_documents: " . $e->getMessage());
        }
    }
    return $documents;
}

// This variable will be used by the dashboard.
$user_documents = isset($_SESSION['user_id']) ? get_user_documents($_SESSION['user_id']) : [];
?>
