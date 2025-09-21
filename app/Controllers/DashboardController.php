<?php

namespace App\Controllers;

use App\Models\Document;

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

        // Fetch the user's documents.
        $documentModel = new Document();
        $documents = $documentModel->findByUser($_SESSION['user_id']);

        // In a future step, we could also fetch admin stats here if the user is an admin.

        $data = [
            'username' => $_SESSION['username'],
            'role_id' => $_SESSION['role_id'],
            'documents' => $documents
        ];

        echo $this->view('dashboard', $data);
    }
}
