<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

class InvitationController
{
    /**
     * Display invitation form.
     */
    public function sendView()
    {
        require_auth();
        require __DIR__ . '/../views/invitation/send.php';
    }


    /**
     * Handle invitation submission.
     * SUPERADMIN → creates company using company_name
     * ADMIN      → invites inside their company
     * MEMBER     → blocked
     */
    public function send()
    {
        require_auth();

        $user  = current_user();
        $email = trim($_POST['email'] ?? '');
        $role  = $_POST['role'] ?? 'member';

        $pdo = db();

        /* ---------------------------------------------------------
         * Validate email
         * --------------------------------------------------------- */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err = "Invalid email format.";
            require __DIR__ . '/../views/invitation/send.php';
            return;
        }


        /* ---------------------------------------------------------
         * DETERMINE COMPANY
         * --------------------------------------------------------- */

        if ($user['role'] === 'superadmin') {

            // SuperAdmin MUST provide company name
            $companyName = trim($_POST['company_name'] ?? '');
            if ($companyName === '') {
                http_response_code(400);
                exit("Company name is required.");
            }

            // Create slug (asp corp → asp-corp)
            $companySlug = strtolower(str_replace(' ', '-', $companyName));

            // Check if this company already exists
            $check = $pdo->prepare("SELECT id FROM companies WHERE slug = ?");
            $check->execute([$companySlug]);

            $existingCompany = $check->fetchColumn();

            if ($existingCompany) {
                $company_id = $existingCompany;
            } else {
                // Create the company
                $insert = $pdo->prepare("
                    INSERT INTO companies (name, slug, created_at)
                    VALUES (?, ?, NOW())
                ");

                $insert->execute([$companyName, $companySlug]);
                $company_id = $pdo->lastInsertId();
            }

        } elseif ($user['role'] === 'admin') {

            // Admin MUST invite inside their own company
            $company_id = $user['company_id'];

            if (!$company_id) {
                http_response_code(403);
                exit("Admin is not assigned to any company.");
            }

        } else {
            http_response_code(403);
            exit("Members cannot send invitations.");
        }


        /* ---------------------------------------------------------
         * CREATE INVITATION RECORD
         * --------------------------------------------------------- */
        $token = bin2hex(random_bytes(20));

        $stmt = $pdo->prepare("
            INSERT INTO invitations (email, token, role, company_id, expires_at, created_at)
            VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 7 DAY), NOW())
        ");

        $stmt->execute([$email, $token, $role, $company_id]);


        /* ---------------------------------------------------------
         * Generate invitation link
         * --------------------------------------------------------- */
        $cfg  = require __DIR__ . '/../config.php';
        $link = rtrim($cfg['APP_URL'], '/') . "/invite/accept?token=" . $token;


        /* ---------------------------------------------------------
         * SHARE UI
         * --------------------------------------------------------- */
        echo "
<div style='background:#111;padding:15px;border:1px solid #444;color:#fff;border-radius:6px'>

    <h3>Invitation Created!</h3>

    <p><strong>Company:</strong> $companyName</p>

    <p><strong>Invite Link:</strong></p>
    <p><a href='$link' style='color:#3b82f6;font-size:16px;'>$link</a></p>

    <hr style='border-color:#444;'>
    <h4>Share Invitation</h4>

    <div style='display:flex;gap:10px;flex-wrap:wrap;margin-top:10px;'>

        <a href='https://wa.me/?text=" . urlencode("Join using this link: $link") . "'
           target='_blank'
           style='padding:8px 12px;background:#25D366;color:white;border-radius:4px;'>WhatsApp</a>

        <a href='mailto:?subject=Invitation&body=" . urlencode("Join: $link") . "'
           target='_blank'
           style='padding:8px 12px;background:#EA4335;color:white;border-radius:4px;'>Gmail</a>

        <a href='https://t.me/share/url?url=" . urlencode($link) . "&text=" . urlencode("Join using this link") . "'
           target='_blank'
           style='padding:8px 12px;background:#0088cc;color:white;border-radius:4px;'>Telegram</a>

        <a href='https://www.linkedin.com/shareArticle?mini=true&url=" . urlencode($link) . "'
           target='_blank'
           style='padding:8px 12px;background:#0A66C2;color:white;border-radius:4px;'>LinkedIn</a>

    </div>

    <br>
    <a href='/dashboard' style='color:#fff;'>Back to Dashboard</a>

</div>";

        exit;
    }


    /* =========================================================
     * ACCEPT INVITATION → CREATE USER ACCOUNT
     * ========================================================= */
    public function accept()
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? null;

        if (!$token) {
            echo "Invalid token";
            exit;
        }

        $pdo = db();

        // Fetch invitation row
        $stmt = $pdo->prepare("
            SELECT *
            FROM invitations
            WHERE token = ?
              AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$token]);

        $inv = $stmt->fetch();

        if (!$inv) {
            echo "Invitation invalid or expired.";
            exit;
        }

        // Logged-in users cannot accept new invite
        if (current_user()) {
            echo "<h3>Please logout before accepting this invitation.</h3>
                  <a href='/logout'>Logout</a>";
            exit;
        }

        // GET → show form
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require __DIR__ . '/../views/invitation/accept.php';
            return;
        }

        // POST → create user account
        $password = trim($_POST['password'] ?? '');

        if (!$password) {
            echo "Password is required.";
            exit;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $email = $inv['email'];
        $name  = explode('@', $email)[0];

        $insert = $pdo->prepare("
            INSERT INTO users (name, email, password, company_id, role, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $insert->execute([
            $name,
            $email,
            $hashed,
            $inv['company_id'],
            $inv['role']
        ]);

        // Mark invitation accepted
        $pdo->prepare("UPDATE invitations SET accepted_at = NOW() WHERE id = ?")
            ->execute([$inv['id']]);

        header("Location: /login?created=1");
        exit;
    }
}
