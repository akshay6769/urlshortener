<?php
// src/controllers/TeamController.php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

class TeamController
{
    /**
     * Display all team members depending on the user role.
     */
    public function index()
    {
        require_auth();

        $user = current_user();
        $pdo  = db();

        /* ---------------------------------------------------------
         * SUPERADMIN:
         *     → Can see all users across all companies
         *
         * ADMIN / MEMBER:
         *     → Can only see users within their own company
         * --------------------------------------------------------- */
        if ($user['role'] === 'superadmin') {

            $stmt = $pdo->query("
                SELECT u.*, c.name AS company_name
                FROM users u
                LEFT JOIN companies c ON c.id = u.company_id
                ORDER BY u.name
            ");

            $members = $stmt->fetchAll();

        } else {

            $stmt = $pdo->prepare("
                SELECT u.*
                FROM users u
                WHERE u.company_id = ?
                ORDER BY u.name
            ");

            $stmt->execute([$user['company_id']]);
            $members = $stmt->fetchAll();
        }

        require __DIR__ . '/../views/dashboard/team.php';
    }
}
