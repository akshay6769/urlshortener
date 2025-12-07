<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../helpers.php';

// Current logged-in user
$user = current_user();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>URL Shortener</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 Dark Theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #0f172a;      /* dark slate */
            color: #e5e7eb;
            min-height: 100vh;
        }

        .app-container {
            padding-top: 70px;
            padding-bottom: 40px;
        }

        .card-dark {
            background-color: #111827;
            border-color: #1f2937;
        }

        .table-dark-custom {
            --bs-table-bg: #020617;
            --bs-table-border-color: #1f2937;
        }
    </style>
</head>


<body>

    <!-- ========================================================= -->
    <!--  TOP NAVBAR                                               -->
    <!-- ========================================================= -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container-fluid">

            <a class="navbar-brand fw-bold" href="/dashboard">
                URL Shortener
            </a>

            <div class="d-flex align-items-center">

                <?php if ($user): ?>

                    <!-- Logged-in user information -->
                    <span class="me-3 text-light small">
                        <?= htmlspecialchars($user['name']) ?>
                        <span class="text-secondary">
                            (<?= htmlspecialchars($user['role']) ?>)
                        </span>
                    </span>

                    <a href="/dashboard"
                       class="btn btn-outline-light btn-sm me-2">
                        Dashboard
                    </a>

                    <a href="/logout"
                       class="btn btn-danger btn-sm">
                        Logout
                    </a>

                <?php else: ?>

                    <!-- Login link when not authenticated -->
                    <a href="/login" class="btn btn-outline-light btn-sm me-2">
                        Login
                    </a>

                <?php endif; ?>

            </div>

        </div>
    </nav>


    <!-- ========================================================= -->
    <!--  MAIN CONTENT WRAPPER                                     -->
    <!-- ========================================================= -->
    <div class="container app-container">

        <?php if (!empty($err)): ?>
            <div class="alert alert-danger"><?= $err ?></div>
        <?php endif; ?>

        <?php if (!empty($ok)): ?>
            <div class="alert alert-success"><?= $ok ?></div>
        <?php endif; ?>

        <!-- Dynamic Page Content -->
        <?= $content ?? '' ?>

    </div>


    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
