<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Users $user */

$this->title = 'Dashboard';
$role = $user->role ?? null;
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>

<div class="container-fluid mt-4 body dash">
    <!-- Header -->
    <div class="dash-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="mb-1"><?= $greeting ?>, <?= Html::encode(explode(' ', $user->full_name)[0] ?? 'there') ?> 👋</h2>
            <p class="text-muted mb-0"><?= date('l, F j, Y') ?> &middot; here's what's happening today.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <?= Html::a('<i class="fas fa-plus me-1"></i> Add Property', ['property/create'], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a('<i class="fas fa-file-signature me-1"></i> New Lease', ['custom/create-lease'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?php if (in_array($role, ['admin', 'manager'])): ?>
                <?= Html::a('<i class="fas fa-user-plus me-1"></i> Add User', ['users/create'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php endif; ?>
            <?= Html::a('<i class="fas fa-chart-bar me-1"></i> Reports', ['report/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-building"></i></div>
                <div>
                    <div class="stat-label">Total Properties</div>
                    <div class="stat-value"><?= $totalProperties ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-file-signature"></i></div>
                <div>
                    <div class="stat-label">Active Leases</div>
                    <div class="stat-value"><?= $activeLeases ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <div class="stat-label">Revenue Collected</div>
                    <div class="stat-value" style="font-size:1.15rem;">TZS <?= number_format($totalCollected, 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff7ed; color:#f59e0b;"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="stat-label">
                        Pending Revenue
                        <?php if ($overdueCount > 0): ?>
                            <span class="badge rounded-pill" style="background:#fee2e2; color:#dc2626; font-size:0.65rem; vertical-align:middle;"><?= $overdueCount ?> overdue</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-value" style="font-size:1.15rem;">TZS <?= number_format($totalPending, 0) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="row mt-4 g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="card-title">Properties by Type</h6>
                    <canvas id="propertyChart1" style="max-height:220px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="card-title">Properties by Status</h6>
                    <canvas id="propertyChart2" style="max-height:220px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent leases + activity -->
    <div class="row mt-4 g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h6 class="card-title mb-0">Recent Leases</h6>
                        <div class="d-flex align-items-center gap-2">
                            <div class="position-relative" style="max-width: 200px;">
                                <i class="bi bi-search position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); color:#939292; font-size:0.85rem;"></i>
                                <input type="text" id="searchInput" class="form-control form-control-sm ps-4" placeholder="Search...">
                            </div>
                            <button id="printBtn" class="btn btn-sm btn-outline-secondary" title="Print"><i class="fas fa-print"></i></button>
                            <button id="exportBtn" class="btn btn-sm btn-outline-secondary" title="Export CSV"><i class="fas fa-file-export"></i></button>
                            <?= Html::a('View all', ['custom/leases'], ['class' => 'btn btn-sm btn-link text-decoration-none']) ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="translationTable">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Tenant</th>
                                    <th>Property</th>
                                    <th>Rent</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLeases as $row): ?>
                                    <tr>
                                        <td><?= Html::encode($row['tenant_name'] ?? '-') ?></td>
                                        <td><?= Html::encode($row['property_name'] ?? '-') ?></td>
                                        <td>TZS <?= number_format($row['price'], 2) ?></td>
                                        <td><?= Html::encode($row['start_date']) ?></td>
                                        <td><?= Html::encode($row['end_date']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= Html::encode($row['status_name'] ?? '-') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentLeases)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">No leases yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">Recent Activity</h6>
                        <?= Html::a('View all', ['custom/notifications'], ['class' => 'btn btn-sm btn-link text-decoration-none']) ?>
                    </div>
                    <div class="activity-feed">
                        <?php foreach ($recentActivity as $notif): ?>
                            <div class="activity-item">
                                <div class="activity-dot" style="<?= $notif->is_read ? 'background:#cbd5e1;' : '' ?>"></div>
                                <div>
                                    <div class="activity-title"><?= Html::encode($notif->title) ?></div>
                                    <div class="activity-msg"><?= Html::encode($notif->message) ?></div>
                                    <div class="activity-time"><?= Yii::$app->formatter->asRelativeTime($notif->created_at) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($recentActivity)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-bell-slash mb-2 d-block" style="font-size:1.5rem;"></i>
                                Nothing new right now.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dash { font-family: 'Inter', 'Roboto', sans-serif; }
    .dash-header h2 { color: #111827; font-weight: 700; }

    .stat-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        height: 100%;
    }
    .stat-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .stat-label { font-size: 0.78rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #111827; }

    .activity-feed { max-height: 340px; overflow-y: auto; }
    .activity-item { display: flex; gap: 0.7rem; padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9; }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot { width: 8px; height: 8px; min-width: 8px; border-radius: 50%; background: #4f46e5; margin-top: 6px; }
    .activity-title { font-weight: 600; font-size: 0.85rem; color: #1f2937; }
    .activity-msg { font-size: 0.8rem; color: #6b7280; }
    .activity-time { font-size: 0.72rem; color: #9ca3af; margin-top: 2px; }
</style>

<!-- Chart.js -->
<script src="<?= Yii::getAlias('@web/lib/chartjs/chart.min.js') ?>"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Chart 1: Property Types
        const ctx1 = document.getElementById('propertyChart1').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($analytics, 'type_name')) ?>,
                datasets: [{
                    label: 'Total',
                    data: <?= json_encode(array_column($analytics, 'total')) ?>,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderRadius: 6,
                    barPercentage: 0.4,
                    categoryPercentage: 0.4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // Chart 2: Property Status
        const ctx2 = document.getElementById('propertyChart2').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($statusAnalytics, 'status_name')) ?>,
                datasets: [{
                    label: 'Total',
                    data: <?= json_encode(array_column($statusAnalytics, 'total')) ?>,
                    backgroundColor: ['#36b9cc', '#f6c23e', '#e74a3b', '#8b5cf6'],
                    borderRadius: 6,
                    barPercentage: 0.4,
                    categoryPercentage: 0.4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // Search filter for the recent-leases table
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('#translationTable tbody tr').forEach(function (row) {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }

        // Print
        const printBtn = document.getElementById('printBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function () { window.print(); });
        }

        // CSV export
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                const rows = [...document.querySelectorAll('#translationTable tr')].filter(r => r.style.display !== 'none');
                const csv = rows.map(row => [...row.querySelectorAll('th,td')].map(cell => '"' + cell.textContent.trim().replace(/"/g, '""') + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'recent-leases.csv';
                link.click();
            });
        }
    });
</script>
