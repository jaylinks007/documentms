<?php

namespace App\Models;

class DocumentVersion
{
    protected $db;

    public function __construct()
    {
        $this->db = new \App\Core\Database();
    }

    /**
     * Create a new document version record.
     * @param array $data Containing document_id, version_number, file_path.
     * @return bool True on success, false on failure.
     */
    public function create(array $data)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "INSERT INTO document_versions (document_id, version_number, file_path) VALUES (?, ?, ?)"
        );
        return $stmt->execute([
            $data['document_id'],
            $data['version_number'],
            $data['file_path']
        ]);
    }
}
