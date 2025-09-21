<?php
// This script should not be accessed directly, but included from another file.
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    exit('No direct script access allowed');
}

require_once __DIR__ . '/database.php';

function get_admin_stats() {
    $database = new Database();
    $conn = $database->connect();

    $stats = [
        'total_users' => 'N/A',
        'total_documents' => 'N/A',
        'pending_approvals' => 'N/A'
    ];

    if ($conn) {
        try {
            // Get total users
            $stmt_users = $conn->query("SELECT COUNT(id) FROM users");
            $stats['total_users'] = $stmt_users->fetchColumn();

            // Get total documents
            $stmt_docs = $conn->query("SELECT COUNT(id) FROM documents");
            $stats['total_documents'] = $stmt_docs->fetchColumn();

            // Get pending approvals
            $stmt_pending = $conn->query("SELECT COUNT(id) FROM approvals WHERE status = 'pending'");
            $stats['pending_approvals'] = $stmt_pending->fetchColumn();

        } catch (PDOException $e) {
            // Log the error for debugging, but don't expose it to the user.
            error_log('Database error in admin_stats.php: ' . $e->getMessage());
        }
    }

    return $stats;
}

// The dashboard will include this file and use the $admin_stats variable.
$admin_stats = get_admin_stats();
?>
