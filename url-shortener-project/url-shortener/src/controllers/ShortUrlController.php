<?php
// src/controllers/ShortUrlController.php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

class ShortUrlController
{
    /**
     * Display the form for creating a short URL.
     * SuperAdmins are not allowed to create short URLs.
     */
    public function create()
    {
        require_auth();

        $user = current_user();

        // SuperAdmin cannot create URLs
        if ($user['role'] === 'superadmin') {
            echo "<p>SuperAdmin cannot create short URLs.</p>
                  <p><a href='/dashboard'>Back</a></p>";
            exit;
        }

        require __DIR__ . '/../views/short/create.php';
    }


    /**
     * Store a newly created short URL.
     */
    public function store()
    {
        require_auth();

        $user = current_user();

        // SuperAdmin forbidden
        if ($user['role'] === 'superadmin') {
            http_response_code(403);
            exit;
        }

        $target = $_POST['target_url'] ?? '';
        $target = sanitize_url($target);

        // Invalid URL
        if (!$target) {
            $err = "Invalid URL";
            require __DIR__ . '/../views/short/create.php';
            return;
        }

        $pdo  = db();
        $code = make_code(6);

        // Insert into DB
        $stmt = $pdo->prepare("
            INSERT INTO short_urls (code, target_url, company_id, created_by, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $code,
            $target,
            $user['company_id'],
            $user['id']
        ]);

        header("Location: /dashboard");
        exit;
    }


    /**
     * Resolve short URL → redirect to long URL and track visit.
     */
    public function resolve($code)
    {
        $pdo = db();

        // Validate short code format
        if (!preg_match('/^[0-9a-zA-Z]{1,64}$/', $code)) {
            http_response_code(404);
            echo "Not found";
            exit;
        }

        // Fetch short URL
        $stmt = $pdo->prepare("SELECT * FROM short_urls WHERE code = ?");
        $stmt->execute([$code]);

        $s = $stmt->fetch();

        if (!$s) {
            http_response_code(404);
            echo "Link not found";
            exit;
        }

        // Increment visit counter
        $pdo->prepare("UPDATE short_urls SET visits_count = visits_count + 1 WHERE id = ?")
            ->execute([$s['id']]);

        // Log visit
        $log = $pdo->prepare("
            INSERT INTO short_url_visits (short_url_id, ip, user_agent)
            VALUES (?, ?, ?)
        ");

        $log->execute([
            $s['id'],
            $_SERVER['REMOTE_ADDR']      ?? null,
            $_SERVER['HTTP_USER_AGENT']  ?? null
        ]);

        // Redirect user
        header("Location: " . $s['target_url'], true, 302);
        exit;
    }


    /**
     * Download CSV list of short URLs based on user role.
     */
    public function downloadCSV()
    {
        require_auth();

        $user = current_user();
        $pdo  = db();

        /* --------------------------
         *  SuperAdmin = all URLs
         *  Admin      = company URLs
         *  Member     = own URLs
         * -------------------------- */

        if ($user['role'] === 'superadmin') {

            $stmt = $pdo->query("
                SELECT s.*, c.name AS company_name, u.name AS creator
                FROM short_urls s
                JOIN companies c ON c.id = s.company_id
                JOIN users u     ON u.id = s.created_by
                ORDER BY s.created_at DESC
            ");

        } elseif ($user['role'] === 'admin') {

            $stmt = $pdo->prepare("
                SELECT s.*, u.name AS creator
                FROM short_urls s
                JOIN users u ON u.id = s.created_by
                WHERE s.company_id = ?
                ORDER BY s.created_at DESC
            ");
            $stmt->execute([$user['company_id']]);

        } else { // member

            $stmt = $pdo->prepare("
                SELECT s.*, u.name AS creator
                FROM short_urls s
                JOIN users u ON u.id = s.created_by
                WHERE s.created_by = ?
                ORDER BY s.created_at DESC
            ");
            $stmt->execute([$user['id']]);
        }

        $rows = $stmt->fetchAll();

        // Output CSV headers
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"short_urls.csv\"");

        $out = fopen('php://output', 'w');

        // CSV Header row
        fputcsv($out, [
            'code',
            'url',
            'company',
            'creator',
            'visits',
            'created_at'
        ]);

        // CSV Data rows
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['code'],
                $r['target_url'],
                $r['company_name'] ?? '',
                $r['creator']      ?? '',
                $r['visits_count'],
                $r['created_at']
            ]);
        }

        fclose($out);
        exit;
    }
}
