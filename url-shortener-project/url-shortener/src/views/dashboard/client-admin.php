<?php ob_start(); ?>

<style>
    .dash-card {
        background: #f7faff;
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
        font-size: 15px;
    }
    .small-muted {
        font-size: 12px;
        color: #6b7280;
    }
</style>

<h2 class="mb-3">Client Admin Dashboard</h2>


<!-- ========================================================== -->
<!--  GENERATED SHORT URLS SECTION                              -->
<!-- ========================================================== -->
<div class="dash-card">

    <div class="dash-header">
        <div class="dash-header-title">Generated Short URLs</div>

        <!-- Generate button -->
        <a href="/short/create" class="btn btn-sm btn-primary">Generate</a>

        <!-- Download dropdown -->
        <div class="dropdown ms-2">
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

    <!-- Filter button (date interval) -->
    <div class="px-3 py-2 d-flex justify-content-start">
        <div class="dropdown">
            <button class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown">
                <?= (isset($_GET['filter']) && $_GET['filter'] !== 'latest') ? ucfirst($_GET['filter']) : "Filter" ?>
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


    <!-- URL TABLE -->
    <table class="table table-sm mb-0">
        <thead>
            <tr>
                <th>Short URL</th>
                <th>Long URL</th>
                <th>Hits</th>
                <th>Creator</th>
                <th>Created On</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($shorts as $s): ?>
                <tr>
                    <td><a href="/<?= $s['code'] ?>"><?= $s['code'] ?></a></td>

                    <td style="max-width:320px" class="text-truncate">
                        <?= $s['target_url'] ?>
                    </td>

                    <td><?= $s['visits_count'] ?></td>
                    <td><?= $s['creator'] ?></td>

                    <td><?= date("d M 'y", strtotime($s['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <!-- Pagination + View All -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">

        <div>
            <span class="small-muted">
                Showing <?= count($shorts) ?> of total <?= $totalShorts ?>
            </span>

            <a href="?view=all" class="btn btn-sm btn-outline-primary">View ALL</a>
        </div>

        <?php if (!isset($_GET['view'])): ?>
            <div class="btn-group btn-group-sm">
                <a href="?page=<?= max(1, $page - 1) ?>" class="btn btn-outline-secondary">&lt; Prev</a>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-outline-secondary">Next &gt;</a>
            </div>
        <?php else: ?>
            <span class="small-muted">All Records Loaded</span>
        <?php endif; ?>
    </div>

</div>



<!-- ========================================================== -->
<!--  TEAM MEMBERS SECTION                                      -->
<!-- ========================================================== -->
<div class="dash-card">

    <div class="dash-header">
        <div class="dash-header-title">Team Members</div>
        <a href="/invite/send" class="btn btn-sm btn-primary">Invite</a>
    </div>

    <table class="table table-sm mb-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Total Generated URLs</th>
                <th>Total URL Hits</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= $m['name'] ?></td>
                    <td><?= $m['email'] ?></td>
                    <td><?= $m['role'] ?></td>
                    <td><?= $m['total_generated'] ?? 0 ?></td>
                    <td><?= $m['total_hits'] ?? 0 ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <!-- Pagination + View All -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">

        <div>
            <span class="small-muted">
                Showing <?= count($members) ?> of total <?= $totalMembers ?>
            </span>

            <a href="?mview=all" class="btn btn-sm btn-outline-primary">View ALL</a>
        </div>

        <?php if (!isset($_GET['mview'])): ?>
            <div class="btn-group btn-group-sm">
                <a href="?mpage=<?= max(1, $mPage - 1) ?>" class="btn btn-outline-secondary">&lt; Prev</a>
                <a href="?mpage=<?= $mPage + 1 ?>" class="btn btn-outline-secondary">Next &gt;</a>
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
