<?php ob_start(); ?>

<style>
    .dash-card {
        background: #f7f7f7;
        border: 1px solid #d0d7de;
        border-radius: 4px;
        margin-bottom: 24px;
    }
    .dash-header {
        padding: 8px 14px;
        border-bottom: 1px solid #d0d7de;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #e5edf5;
    }
    .dash-header-title {
        font-weight: 600;
        font-size: 15px;
    }
    .small-muted {
        font-size: 12px;
        color: #6b7280;
    }
</style>

<h2 class="mb-3">Super Admin Dashboard</h2>


<!-- =============================================================== -->
<!--  CLIENTS SECTION                                                -->
<!-- =============================================================== -->
<div class="dash-card">

    <div class="dash-header">
        <div class="dash-header-title">Clients</div>
        <a href="/invite/send" class="btn btn-sm btn-primary">Invite</a>
    </div>

    <div class="dash-body">

        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Users</th>
                    <th>Total Generated URLs</th>
                    <th>Total URL Hits</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($companies as $c): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($c['name']) ?><br>
                            <span class="small-muted"><?= htmlspecialchars($c['slug']) ?>@example.com</span>
                        </td>
                        <td><?= $c['total_users'] ?></td>
                        <td><?= $c['total_urls'] ?></td>
                        <td><?= $c['total_visits'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination + View All -->
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">

            <div>
                <span class="small-muted">
                    Showing <?= count($companies) ?> of total <?= $totalCompanies ?>
                </span>

                <a href="?cview=all" class="btn btn-sm btn-outline-primary ms-2">
                    View All
                </a>
            </div>

            <?php if (!isset($_GET['cview'])): ?>
                <div class="btn-group btn-group-sm">
                    <a href="?cpage=<?= max(1, $clientPage - 1) ?>" class="btn btn-outline-secondary">&lt; Prev</a>
                    <a href="?cpage=<?= $clientPage + 1 ?>" class="btn btn-outline-secondary">Next &gt;</a>
                </div>
            <?php else: ?>
                <span class="small-muted">All Records Loaded</span>
            <?php endif; ?>

        </div>

    </div>
</div>



<!-- =============================================================== -->
<!--  GENERATED SHORT URLS SECTION                                   -->
<!-- =============================================================== -->
<div class="dash-card">

    <div class="dash-header">
        <div>
            <div class="dash-header-title">Generated Short URLs</div>
            <div class="small-muted">View and Download based on Date Interval</div>
        </div>

        <!-- Download Dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                    data-bs-toggle="dropdown">
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


    <!-- Filter Button -->
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
                <th>Company</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($shorts as $s): ?>
                <tr>
                    <td><a href="/<?= $s['code'] ?>"><?= $s['code'] ?></a></td>

                    <td class="text-truncate" style="max-width: 320px;">
                        <?= $s['target_url'] ?>
                    </td>

                    <td><?= $s['visits_count'] ?></td>
                    <td><?= $s['company_name'] ?></td>

                    <td><?= date("d M 'y", strtotime($s['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <!-- Pagination + View All -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">

        <div>
            <span class="small-muted">
                Showing <?= count($shorts) ?> of total <?= $totalShortUrls ?>
            </span>

            <a href="?view=all" class="btn btn-sm btn-outline-primary">View All</a>
        </div>

        <?php if (!isset($_GET['view'])): ?>
            <div class="btn-group btn-group-sm">
                <a href="?spage=<?= max(1, $urlPage - 1) ?>" class="btn btn-outline-secondary">&lt; Prev</a>
                <a href="?spage=<?= $urlPage + 1 ?>" class="btn btn-outline-secondary">Next &gt;</a>
            </div>
        <?php else: ?>
            <span class="small-muted">All Records Loaded</span>
        <?php endif; ?>

    </div>

</div>


<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
