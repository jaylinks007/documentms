<?php

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

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

        // In the future, we will fetch user documents and admin stats here
        // and pass them to the view.

        $data = [
            'username' => $_SESSION['username'],
            'role_id' => $_SESSION['role_id']
        ];

        echo $this->view('dashboard', $data);
    }
}
