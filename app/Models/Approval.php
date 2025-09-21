<?php

namespace App\Models;

class Approval
{
    protected $db;

    public function __construct()
    {
        $this->db = new \App\Core\Database();
    }

    /**
     * Create a new approval request.
     * @param array $data Containing document_version_id and approver_id.
     * @return bool True on success, false on failure.
     */
    public function create(array $data)
    {
        $conn = $this->db->connect();

        // Check if a request for this version already exists to prevent duplicates.
        $checkStmt = $conn->prepare("SELECT id FROM approvals WHERE document_version_id = ? AND approver_id = ?");
        $checkStmt->execute([$data['document_version_id'], $data['approver_id']]);
        if ($checkStmt->fetch()) {
            // An approval request for this version and user already exists.
            // You might want to handle this case, e.g., by returning an error message.
            return false;
        }

        $stmt = $conn->prepare(
            "INSERT INTO approvals (document_version_id, approver_id, status) VALUES (?, ?, 'pending')"
        );
        return $stmt->execute([
            $data['document_version_id'],
            $data['approver_id']
        ]);
    }
}
