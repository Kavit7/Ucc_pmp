<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Users $user */

$this->title = 'Dashboard';
$role = $user->role ?? null;
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

// Role-tailored section visibility: everyone gets properties/leases/maintenance
// context, but revenue detail is for admin/manager/accountant, and property
// composition charts are for admin/manager - a technician's day is about
// requests and property status, not revenue.
$showRevenue = in_array($role, ['admin', 'manager', 'accountant']);
$showPropertyMix = in_array($role, ['admin', 'manager']);
$showLeaseTable = in_array($role, ['admin', 'manager', 'accountant']);
$showMaintenance = in_array($role, ['admin', 'manager', 'technician']);
$showExpiryAlert = in_array($role, ['admin', 'manager', 'accountant']);
?>

<div class="container-fluid mt-4 body dash">
    <!-- Header -->
    <div class="dash-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="mb-1"><?= $greeting ?>, <?= Html::encode(explode(' ', $user->full_name)[0] ?? 'there') ?> 👋</h2>
            <p class="text-muted mb-0"><?= date('l, F j, Y') ?> &middot; here's what's happening today.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <?php if (in_array($role, ['admin', 'manager'])): ?>
                <?= Html::a('<i class="fas fa-plus me-1"></i> Add Property', ['property/create'], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a('<i class="fas fa-file-signature me-1"></i> New Lease', ['custom/create-lease'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                <?= Html::a('<i class="fas fa-user-plus me-1"></i> Add User', ['users/create'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php endif; ?>
            <?php if ($showMaintenance): ?>
                <?= Html::a('<i class="fas fa-tools me-1"></i> Maintenance', ['maintenance/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php endif; ?>
            <?php if ($showRevenue): ?>
                <?= Html::a('<i class="fas fa-chart-bar me-1"></i> Reports', ['report/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($showExpiryAlert && !empty($expiringLeases)): ?>
        <div class="expiry-alert mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-triangle-exclamation" style="color:#d97706;"></i>
                <strong>Leases Expiring Soon</strong>
                <span class="badge rounded-pill" style="background:#fef3c7; color:#92400e;"><?= count($expiringLeases) ?> in the next 30 days</span>
            </div>
            <div class="expiry-list">
                <?php foreach ($expiringLeases as $lease): ?>
                    <div class="expiry-row">
                        <span><?= Html::encode($lease['property_name'] ?? '-') ?> &middot; <?= Html::encode($lease['tenant_name'] ?? '-') ?></span>
                        <span class="text-muted small">ends <?= Html::encode($lease['lease_end_date']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stat cards -->
    <div class="row g-3">
        <div class="col-6 col-lg-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-building"></i></div>
                <div>
                    <div class="stat-label">Total Properties</div>
                    <div class="stat-value"><?= $totalProperties ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#f0fdfa; color:#0d9488;"><i class="fas fa-door-open"></i></div>
                <div>
                    <div class="stat-label">Occupancy Rate</div>
                    <div class="stat-value"><?= $occupancyRate ?>%</div>
                    <div class="stat-sub"><?= $occupiedProperties ?> of <?= $totalProperties ?> occupied</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-file-signature"></i></div>
                <div>
                    <div class="stat-label">Active Leases</div>
                    <div class="stat-value"><?= $activeLeases ?></div>
                </div>
            </div>
        </div>

        <?php if ($showRevenue): ?>
            <div class="col-6 col-lg-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <div class="stat-label">Revenue Collected</div>
                        <div class="stat-value" style="font-size:1.15rem;">TZS <?= number_format($totalCollected, 0) ?></div>
                        <div class="stat-sub <?= $revenueChangePct >= 0 ? 'trend-up' : 'trend-down' ?>">
                            <i class="fas fa-arrow-<?= $revenueChangePct >= 0 ? 'up' : 'down' ?>"></i>
                            <?= abs($revenueChangePct) ?>% vs last month
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
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
        <?php endif; ?>

        <?php if ($showMaintenance): ?>
            <div class="col-6 col-lg-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef2f2; color:#ef4444;"><i class="fas fa-tools"></i></div>
                    <div>
                        <div class="stat-label">
                            <?= $role === 'technician' ? 'Assigned to Me' : 'Open Requests' ?>
                            <?php if ($urgentMaintenanceCount > 0): ?>
                                <span class="badge rounded-pill" style="background:#fee2e2; color:#dc2626; font-size:0.65rem; vertical-align:middle;"><?= $urgentMaintenanceCount ?> urgent</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-value"><?= $role === 'technician' ? $myMaintenanceCount : $openMaintenanceCount ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Charts -->
    <div class="row mt-4 g-3">
        <?php if ($showRevenue): ?>
            <div class="col-lg-<?= $showPropertyMix ? '6' : '12' ?>">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="card-title">Revenue Trend (last 6 months)</h6>
                        <canvas id="revenueChart" style="max-height:240px;"></canvas>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($showPropertyMix): ?>
            <div class="col-lg-<?= $showRevenue ? '6' : '6' ?>">
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
        <?php elseif ($showMaintenance): ?>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="card-title">Properties by Status</h6>
                        <canvas id="propertyChart2" style="max-height:220px;"></canvas>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent leases + activity -->
    <div class="row mt-4 g-3">
        <?php if ($showLeaseTable): ?>
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
        <?php endif; ?>

        <div class="col-lg-<?= $showLeaseTable ? '4' : '12' ?>">
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
    .stat-sub { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }
    .stat-sub.trend-up { color: #16a34a; }
    .stat-sub.trend-down { color: #dc2626; }

    .expiry-alert {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 1rem 1.25rem;
    }
    .expiry-list { display: flex; flex-direction: column; gap: 4px; }
    .expiry-row { display: flex; justify-content: space-between; font-size: 0.85rem; padding: 3px 0; }

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
        <?php if ($showRevenue): ?>
        // Revenue trend chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($revenueTrend, 'label')) ?>,
                datasets: [
                    {
                        label: 'Collected',
                        data: <?= json_encode(array_column($revenueTrend, 'collected')) ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        tension: 0.35,
                        fill: true,
                    },
                    {
                        label: 'Pending',
                        data: <?= json_encode(array_column($revenueTrend, 'pending')) ?>,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        tension: 0.35,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true, position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });
        <?php endif; ?>

        <?php if ($showPropertyMix): ?>
        // Chart: Property Types
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
        <?php endif; ?>

        <?php if ($showPropertyMix || $showMaintenance): ?>
        // Chart: Property Status
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
        <?php endif; ?>

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
