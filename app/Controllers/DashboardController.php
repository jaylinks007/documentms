<?php

namespace App\Controllers;

use App\Models\Document;
use App\Models\Approval;

class DashboardController extends Controller
{
    /**
     * Show the main user dashboard.
     */
    public function index()
    {
        // Protect the route by checking for a valid session.
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Fetch the user's own documents.
        $documentModel = new Document();
        $documents = $documentModel->findByUser($_SESSION['user_id']);

        // Fetch documents awaiting the user's approval.
        $approvalModel = new Approval();
        $pendingApprovals = $approvalModel->findPendingForUser($_SESSION['user_id']);

        $data = [
            'username' => $_SESSION['username'],
            'role_id' => $_SESSION['role_id'],
            'documents' => $documents,
            'pending_approvals' => $pendingApprovals
        ];

        echo $this->view('dashboard', $data);
    }
}
