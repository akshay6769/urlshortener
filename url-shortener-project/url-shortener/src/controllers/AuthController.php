<?php
// src/controllers/AuthController.php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

class AuthController
{
    /**
     * Show the login form.
     */
    public function loginView()
    {
        require __DIR__ . '/../auth/login.php';
    }


    /**
     * Handle login form submission.
     */
    public function login()
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $pdo  = db();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        // Validate password
        if ($user && password_verify($password, $user['password'])) {
            login_user_by_id($user['id']);
            header('Location: /dashboard');
            exit;
        }

        // Invalid login → reload login page with error
        $err = "Invalid credentials";
        require __DIR__ . '/../auth/login.php';
    }


    /**
     * Signup is not allowed — system is invitation-only.
     */
    public function registerView()
    {
        $err = "Direct signup is not allowed. Please use an invitation link.";

        ob_start();
        ?>
            <div class="card card-dark shadow">
                <div class="card-body">
                    <h3 class="card-title mb-3">Signup Disabled</h3>

                    <p>Users can only join via invitations sent by a Client Admin or Super Admin.</p>

                    <a href="/login" class="btn btn-primary">Back to Login</a>
                </div>
            </div>
        <?php
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }


    /**
     * Block POST /register entirely.
     */
    public function register()
    {
        http_response_code(403);
        echo "Direct registration is not allowed.";
        exit;
    }


    /**
     * Logout user and redirect to login page.
     */
    public function logout()
    {
        logout_user();
        header('Location: /login');
        exit;
    }
}
