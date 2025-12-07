<?php ob_start(); ?>

<style>
    .dash-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        margin-bottom: 22px;
    }
    .dash-header {
        padding: 8px 14px;
        border-bottom: 1px solid #cbd5e1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #e8f1fb;
    }
    .dash-header-title {
        font-weight: 600;
        font-size: 16px;
        color: #00416a;
    }
    .small-muted {
        font-size: 12px;
        color: #6b7280;
    }
</style>

<h2 class="mb-3 text-danger">Client Member Dashboard</h2>


<!-- ========================================================== -->
<!--  GENERATED SHORT URL SECTION                               -->
<!-- ========================================================== -->
<div class="dash-card">

    <div class="dash-header">
        <div class="dash-header-title">Generated Short URLs</div>

        <div class="d-flex align-items-center">

            <!-- Generate Button -->
            <a href="/short/create" class="btn btn-sm btn-primary me-2">Generate</a>

            <!-- Download Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    Download
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="?filter=month">This Month</a></li>
                    <li><a class="dropdown-item" href="?filter=last_month">Last Month</a></li>
                    <li><a class="dropdown-item" href="?filter=week">Last Week</a></li>
                    <li><a class="dropdown-item" href="?filter=today">Today</a></li>

                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/short/download">Download CSV</a></li>
                </ul>
            </div>

        </div>
    </div>

    <!-- Filter Dropdown -->
    <div class="px-3 py-2 d-flex justify-content-start">
        <div class="dropdown">
            <button class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown">
                <?= isset($_GET['filter']) && $_GET['filter'] !== 'latest'
                    ? ucfirst($_GET['filter'])
                    : "Filter" ?>
            </button>

            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?filter=month">This Month</a></li>
                <li><a class="dropdown-item" href="?filter=last_month">Last Month</a></li>
                <li><a class="dropdown-item" href="?filter=week">Last Week</a></li>
                <li><a class="dropdown-item" href="?filter=today">Today</a></li>
                <li><a class="dropdown-item" href="?filter=latest">Clear Filter</a></li>
            </ul>
        </div>
    </div>


    <!-- URL Table -->
    <table class="table table-sm mb-0">
        <thead>
            <tr>
                <th>Short URL</th>
                <th>Long URL</th>
                <th>Hits</th>
                <th>Created On</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($shorts as $s): ?>
                <tr>
                    <td><a href="/<?= $s['code'] ?>"><?= $s['code'] ?></a></td>

                    <td class="text-truncate" style="max-width: 350px;">
                        <?= htmlspecialchars($s['target_url']) ?>
                    </td>

                    <td><?= $s['visits_count'] ?></td>

                    <td><?= date("d M 'y", strtotime($s['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <!-- Bottom Bar (Pagination + Count) -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">

        <span class="small-muted">
            Showing <?= count($shorts) ?> of total <?= $totalShorts ?>
        </span>

        <div class="btn-group btn-group-sm">
            <a href="?page=<?= max(1, $page - 1) ?>" class="btn btn-outline-secondary">&lt; Prev</a>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-outline-secondary">Next &gt;</a>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
