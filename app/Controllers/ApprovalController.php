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

    /**
     * Show a single approval request page.
     */
    public function show()
    {
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo "Bad Request: No approval ID specified.";
            exit();
        }

        $approvalId = $_GET['id'];
        $approvalModel = new \App\Models\Approval();
        $approval = $approvalModel->findById($approvalId);

        // Security check: ensure the request exists and the logged-in user is the approver.
        if (!$approval || $approval['approver_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo "Forbidden: You are not authorized to view this approval request.";
            exit();
        }

        echo $this->view('approvals.show', ['approval' => $approval]);
    }

    /**
     * Update the status of an approval request.
     */
    public function update()
    {
        if (empty($_POST['approval_id']) || empty($_POST['action'])) {
            http_response_code(400);
            echo "Bad Request: Missing required form data.";
            exit();
        }

        $approvalId = $_POST['approval_id'];
        $action = $_POST['action'];
        $comments = trim($_POST['comments'] ?? '');

        // Validate action
        if (!in_array($action, ['approved', 'rejected'])) {
            http_response_code(400);
            echo "Bad Request: Invalid action specified.";
            exit();
        }

        $approvalModel = new \App\Models\Approval();
        $approval = $approvalModel->findById($approvalId);

        // Security check: ensure the request exists and the logged-in user is the approver.
        if (!$approval || $approval['approver_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo "Forbidden: You are not authorized to update this approval request.";
            exit();
        }

        // Update the approval status
        $success = $approvalModel->updateStatus($approvalId, $action, $comments);

        if ($success) {
            header('Location: /dashboard?approval_action=success');
        } else {
            header('Location: /approvals?id=' . $approvalId . '&error=update_failed');
        }
        exit();
    }
}
