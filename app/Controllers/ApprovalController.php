<?php

namespace App\Controllers;

use App\Models\Approval;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;

class ApprovalController extends Controller
{
    /**
     * Show the form for creating a new approval request.
     */
    public function create()
    {
        if (empty($_GET['document_id'])) {
            header('Location: /dashboard?error=no_document_specified');
            exit();
        }

        $documentId = $_GET['document_id'];
        $docModel = new Document();
        $document = $docModel->findById($documentId);

        // Security check: Ensure the document exists and belongs to the logged-in user.
        if (!$document || $document['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo "Forbidden: You do not have permission to send this document for approval.";
            exit();
        }

        $userModel = new User();
        // Get all users except the current user to populate the dropdown.
        $users = $userModel->getAll($_SESSION['user_id']);

        echo $this->view('approvals.create', [
            'document' => $document,
            'users' => $users
        ]);
    }

    /**
     * Store a new approval request in the database.
     */
    public function store()
    {
        if (empty($_POST['document_id']) || empty($_POST['approver_id'])) {
            header('Location: /dashboard?error=missing_data');
            exit();
        }

        $documentId = $_POST['document_id'];
        $approverId = $_POST['approver_id'];

        // Security check again before creating the approval.
        $docModel = new Document();
        $document = $docModel->findById($documentId);
        if (!$document || $document['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo "Forbidden: You do not have permission to send this document for approval.";
            exit();
        }

        $versionModel = new DocumentVersion();
        $latestVersionId = $versionModel->getLatestVersionId($documentId);

        if (!$latestVersionId) {
            header('Location: /dashboard?error=no_version_found');
            exit();
        }

        $approvalModel = new Approval();
        $success = $approvalModel->create([
            'document_version_id' => $latestVersionId,
            'approver_id' => $approverId
        ]);

        if ($success) {
            header('Location: /dashboard?approval_sent=true');
        } else {
            // This could fail if the request already exists, as per our model logic.
            header('Location: /approvals/create?document_id=' . $documentId . '&error=already_exists');
        }
        exit();
    }
}
