<?php

namespace App\Models;

class Document
{
    protected $db;

    public function __construct()
    {
        $this->db = new \App\Core\Database();
    }

    /**
     * Create a new document record.
     * @param array $data Containing user_id, title, description.
     * @return string The ID of the newly created document.
     */
    public function create(array $data)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "INSERT INTO documents (user_id, title, description) VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['description']
        ]);
        return $conn->lastInsertId();
    }

    /**
     * Find all documents for a given user, showing the latest version of each.
     * @param int $userId The ID of the user.
     * @return array An array of documents.
     */
    public function findByUser($userId)
    {
        $conn = $this->db->connect();
        $sql = "SELECT
                    d.id,
                    d.title,
                    d.description,
                    dv.file_path,
                    dv.created_at AS upload_date
                FROM documents d
                INNER JOIN document_versions dv ON d.id = dv.document_id
                WHERE d.user_id = ?
                AND dv.version_number = (
                    SELECT MAX(version_number)
                    FROM document_versions
                    WHERE document_id = d.id
                )
                ORDER BY d.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find a single document by its ID.
     * @param int $id The ID of the document.
     * @return mixed The document data or false if not found.
     */
    public function findById($id)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
