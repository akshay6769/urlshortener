<?php
// src/controllers/DashboardController.php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

class DashboardController
{
    /**
     * Handle all dashboard routes based on user role.
     */
    public function index()
    {
        require_auth();

        $user = current_user();
        $pdo  = db();

        /* ---------------------------------------------------------
         *  DATE FILTERS FOR ALL URL LISTS
         * --------------------------------------------------------- */
        $filter = $_GET['filter'] ?? 'latest';

        switch ($filter) {
            case 'today':
                $whereDate = "AND DATE(s.created_at) = CURDATE()";
                break;

            case 'week':
                $whereDate = "AND s.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;

            case 'month':
                $whereDate = "AND s.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;

            case 'last_month':
                $whereDate = "
                    AND YEAR(s.created_at)  = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                    AND MONTH(s.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                ";
                break;

            default:
                $whereDate = ""; // no filter
        }


        /* ---------------------------------------------------------
         *  SUPERADMIN DASHBOARD
         * --------------------------------------------------------- */
        if ($user['role'] === 'superadmin') {

            /* -----------------------------------------------------
             *  CLIENTS PAGINATION + VIEW ALL SUPPORT
             * ----------------------------------------------------- */
            $clientViewAll = isset($_GET['cview']) && $_GET['cview'] === 'all';

            $clientLimit  = $clientViewAll ? 1000 : 2;
            $clientPage   = $clientViewAll ? 1 : max(1, (int)($_GET['cpage'] ?? 1));
            $clientOffset = ($clientPage - 1) * $clientLimit;

            // Count companies
            $totalCompanies = (int)$pdo
                ->query("SELECT COUNT(*) FROM companies")
                ->fetchColumn();

            // Fetch companies
            $stmt = $pdo->prepare("
                SELECT c.*,
                       (SELECT COUNT(*) FROM users WHERE company_id = c.id) AS total_users,
                       (SELECT COUNT(*) FROM short_urls WHERE company_id = c.id) AS total_urls,
                       (SELECT COALESCE(SUM(visits_count), 0)
                        FROM short_urls WHERE company_id = c.id) AS total_visits
                FROM companies c
                ORDER BY c.name ASC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindValue(':limit',  $clientLimit,  PDO::PARAM_INT);
            $stmt->bindValue(':offset', $clientOffset, PDO::PARAM_INT);
            $stmt->execute();

            $companies = $stmt->fetchAll();


            /* -----------------------------------------------------
             *  SHORT URLS PAGINATION (with View All)
             * ----------------------------------------------------- */
            $viewAll = (($_GET['view'] ?? '') === 'all');

            $urlLimit  = $viewAll ? 1000 : 2;
            $urlPage   = $viewAll ? 1 : max(1, (int)($_GET['spage'] ?? 1));
            $urlOffset = ($urlPage - 1) * $urlLimit;

            // Count short URLs
            $totalShortUrls = (int)$pdo
                ->query("SELECT COUNT(*) FROM short_urls s WHERE 1=1 $whereDate")
                ->fetchColumn();

            // Fetch short URLs
            $urlStmt = $pdo->prepare("
                SELECT s.*, u.name AS creator, c.name AS company_name
                FROM short_urls s
                JOIN users     u ON u.id = s.created_by
                JOIN companies c ON c.id = s.company_id
                WHERE 1=1 $whereDate
                ORDER BY s.created_at DESC
                LIMIT :limit OFFSET :offset
            ");

            $urlStmt->bindValue(':limit',  $urlLimit,  PDO::PARAM_INT);
            $urlStmt->bindValue(':offset', $urlOffset, PDO::PARAM_INT);
            $urlStmt->execute();

            $shorts = $urlStmt->fetchAll();

            require __DIR__ . '/../views/dashboard/superadmin.php';
            return;
        }



        /* ---------------------------------------------------------
         *  CLIENT ADMIN DASHBOARD
         * --------------------------------------------------------- */
        if ($user['role'] === 'admin') {

            /* ------------------------------
             *  COMPANY URL PAGINATION
             * ------------------------------ */
            $limit  = 2;
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $offset = ($page - 1) * $limit;

            // Count URLs
            $countStmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM short_urls s
                WHERE s.company_id = ? $whereDate
            ");
            $countStmt->execute([$user['company_id']]);
            $totalShorts = (int)$countStmt->fetchColumn();

            // Fetch URLs
            $stmt = $pdo->prepare("
                SELECT s.*, u.name AS creator
                FROM short_urls s
                JOIN users u ON u.id = s.created_by
                WHERE s.company_id = ? $whereDate
                ORDER BY s.created_at DESC
                LIMIT $limit OFFSET $offset
            ");
            $stmt->execute([$user['company_id']]);
            $shorts = $stmt->fetchAll();


            /* ------------------------------
             *  TEAM MEMBERS PAGINATION
             * ------------------------------ */
            $mViewAll = isset($_GET['mview']) && $_GET['mview'] === 'all';

            $mLimit  = $mViewAll ? 500 : 3;
            $mPage   = $mViewAll ? 1 : max(1, (int)($_GET['mpage'] ?? 1));
            $mOffset = ($mPage - 1) * $mLimit;

            // Count members
            $mCountStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE company_id = ?");
            $mCountStmt->execute([$user['company_id']]);
            $totalMembers = (int)$mCountStmt->fetchColumn();

            // Load members + stats
            $membersStmt = $pdo->prepare("
                SELECT u.*,
                       (SELECT COUNT(*) FROM short_urls WHERE created_by = u.id) AS total_generated,
                       (SELECT COALESCE(SUM(visits_count),0)
                        FROM short_urls WHERE created_by = u.id) AS total_hits
                FROM users u
                WHERE u.company_id = ?
                ORDER BY u.name
                LIMIT $mLimit OFFSET $mOffset
            ");
            $membersStmt->execute([$user['company_id']]);
            $members = $membersStmt->fetchAll();

            require __DIR__ . '/../views/dashboard/client-admin.php';
            return;
        }



        /* ---------------------------------------------------------
         *  CLIENT MEMBER DASHBOARD
         * --------------------------------------------------------- */
        if ($user['role'] === 'member') {

            $limit  = 2;
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $offset = ($page - 1) * $limit;

            // Count URLs
            $countStmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM short_urls s
                WHERE s.created_by = ? $whereDate
            ");
            $countStmt->execute([$user['id']]);
            $totalShorts = (int)$countStmt->fetchColumn();

            // Fetch URLs
            $stmt = $pdo->prepare("
                SELECT *
                FROM short_urls s
                WHERE s.created_by = ? $whereDate
                ORDER BY s.created_at DESC
                LIMIT $limit OFFSET $offset
            ");
            $stmt->execute([$user['id']]);
            $shorts = $stmt->fetchAll();

            require __DIR__ . '/../views/dashboard/client-member.php';
            return;
        }



        /* ---------------------------------------------------------
         *  UNKNOWN ROLE (fail-safe)
         * --------------------------------------------------------- */
        echo "Unknown role.";
    }
}
