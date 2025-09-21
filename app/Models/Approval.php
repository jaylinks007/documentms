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

    /**
     * Find all pending approval requests for a given user.
     * @param int $userId The ID of the approver.
     * @return array An array of pending approvals with document details.
     */
    public function findPendingForUser($userId)
    {
        $conn = $this->db->connect();
        $sql = "SELECT
                    a.id as approval_id,
                    d.title,
                    u.username as sender_name,
                    a.created_at as request_date
                FROM approvals a
                JOIN document_versions dv ON a.document_version_id = dv.id
                JOIN documents d ON dv.document_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE a.approver_id = ? AND a.status = 'pending'
                ORDER BY a.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update the status and comments of an approval request.
     * @param int $approvalId The ID of the approval request.
     * @param string $status The new status ('approved' or 'rejected').
     * @param string|null $comments The comments from the approver.
     * @return bool True on success, false on failure.
     */
    public function updateStatus($approvalId, $status, $comments = null)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "UPDATE approvals SET status = ?, comments = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $comments, $approvalId]);
    }

    /**
     * Find a single approval request by its ID, joining with other tables for full details.
     * @param int $approvalId The ID of the approval request.
     * @return mixed The approval data or false if not found.
     */
    public function findById($approvalId)
    {
        $conn = $this->db->connect();
        $sql = "SELECT
                    a.id as approval_id,
                    a.approver_id,
                    d.title,
                    d.description,
                    dv.file_path,
                    u.username as sender_name,
                    a.created_at as request_date
                FROM approvals a
                JOIN document_versions dv ON a.document_version_id = dv.id
                JOIN documents d ON dv.document_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE a.id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$approvalId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
