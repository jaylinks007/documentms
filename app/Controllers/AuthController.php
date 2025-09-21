<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        // The view method is inherited from the base Controller.
        echo $this->view('auth.register');
    }

    /**
     * Store a new user in the database.
     */
    public function store()
    {
        // Basic server-side validation.
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            // In a real app, use a more robust validation and flash messaging system.
            header('Location: /register?error=empty_fields');
            exit();
        }

        $userModel = new User();

        // Check if username already exists.
        if ($userModel->findByUsername($_POST['username'])) {
            header('Location: /register?error=user_exists');
            exit();
        }

        // Prepare data array from POST request.
        $data = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'], // Hashing is done in the model.
            'organisation_name' => !empty(trim($_POST['organisation_name'])) ? trim($_POST['organisation_name']) : null,
            'department' => !empty(trim($_POST['department'])) ? trim($_POST['department']) : null,
            'project_name' => !empty(trim($_POST['project_name'])) ? trim($_POST['project_name']) : null,
        ];

        // Attempt to create the user.
        if ($userModel->create($data)) {
            // Redirect to the login page with a success message.
            header('Location: /login?registration=success');
            exit();
        } else {
            // Redirect back with a generic error if creation fails.
            header('Location: /register?error=creation_failed');
            exit();
        }
    }

    /**
     * Show the login form.
     */
    public function login()
    {
        echo $this->view('auth.login');
    }

    /**
     * Authenticate a user and create a session.
     */
    public function authenticate()
    {
        if (empty($_POST['username']) || empty($_POST['password'])) {
            header('Location: /login?error=empty_fields');
            exit();
        }

        $userModel = new User();
        $user = $userModel->findByUsername(trim($_POST['username']));

        if ($user && password_verify($_POST['password'], $user['password'])) {
            // Regenerate session ID to prevent session fixation attacks.
            session_regenerate_id(true);

            // Set session variables.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            // Redirect to the dashboard.
            header('Location: /dashboard');
            exit();
        }

        // If authentication fails, redirect back to the login page.
        header('Location: /login?error=invalid_credentials');
        exit();
    }

    /**
     * Destroy the user session (logout).
     */
    public function logout()
    {
        session_unset();
        session_destroy();

        header('Location: /login?logout=success');
        exit();
    }
}
