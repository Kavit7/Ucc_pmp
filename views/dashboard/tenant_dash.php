<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Users $user */
/** @var app\models\Lease[] $leases */
/** @var app\models\Bill[] $bills */
/** @var float $totalDue */
/** @var int $overdueCount */
/** @var int $activeLeases */
/** @var app\models\MaintenanceRequest[] $maintenanceRequests */
/** @var app\models\Notification[] $recentActivity */

$this->title = 'My Dashboard';
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>

<div class="container-fluid mt-4 body dash">
    <div class="dash-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="mb-1"><?= $greeting ?>, <?= Html::encode(explode(' ', $user->full_name)[0] ?? 'there') ?> 👋</h2>
            <p class="text-muted mb-0"><?= date('l, F j, Y') ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <?= Html::a('<i class="fas fa-tools me-1"></i> Report an Issue', ['maintenance/create'], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a('<i class="fas fa-file-invoice me-1"></i> My Bills', ['custom/bill'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="row g-3">
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
                <div class="stat-icon" style="background:#fff7ed; color:#f59e0b;"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="stat-label">
                        Amount Due
                        <?php if ($overdueCount > 0): ?>
                            <span class="badge rounded-pill" style="background:#fee2e2; color:#dc2626; font-size:0.65rem; vertical-align:middle;"><?= $overdueCount ?> overdue</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-value" style="font-size:1.15rem;">TZS <?= number_format($totalDue, 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-tools"></i></div>
                <div>
                    <div class="stat-label">Open Requests</div>
                    <div class="stat-value"><?= count(array_filter($maintenanceRequests, fn($r) => strtolower($r->status->list_Name ?? '') !== 'resolved' && strtolower($r->status->list_Name ?? '') !== 'closed')) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-building"></i></div>
                <div>
                    <div class="stat-label">Total Leases</div>
                    <div class="stat-value"><?= count($leases) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="card-title mb-3">My Leases</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Property</th>
                                    <th>Rent</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leases as $lease): ?>
                                    <tr>
                                        <td><?= Html::encode($lease->property->property_name ?? '-') ?></td>
                                        <td>TZS <?= number_format($lease->propertyPrice->unit_amount ?? 0, 2) ?></td>
                                        <td><?= Html::encode($lease->lease_start_date) ?></td>
                                        <td><?= Html::encode($lease->lease_end_date) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= Html::encode($lease->statusLabel->list_Name ?? '-') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($leases)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">You have no leases yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">My Bills</h6>
                        <?= Html::a('View all', ['custom/bill'], ['class' => 'btn btn-sm btn-link text-decoration-none']) ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Property</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($bills, 0, 6) as $bill): ?>
                                    <tr>
                                        <td><?= Html::encode($bill->lease->property->property_name ?? '-') ?></td>
                                        <td>TZS <?= number_format($bill->amount, 2) ?></td>
                                        <td><?= Html::encode($bill->due_date) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= Html::encode($bill->billStatus->list_Name ?? '-') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($bills)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">No bills yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">My Maintenance Requests</h6>
                        <?= Html::a('View all', ['maintenance/index'], ['class' => 'btn btn-sm btn-link text-decoration-none']) ?>
                    </div>
                    <?php foreach ($maintenanceRequests as $req): ?>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <div class="activity-title"><?= Html::encode($req->title) ?></div>
                                <div class="activity-msg"><?= Html::encode($req->property->property_name ?? '-') ?> &middot; <?= Html::encode($req->status->list_Name ?? '-') ?></div>
                                <div class="activity-time"><?= Yii::$app->formatter->asRelativeTime($req->created_at) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($maintenanceRequests)): ?>
                        <div class="text-center text-muted py-4">No requests yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <h6 class="card-title mb-3">Recent Activity</h6>
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
                            <div class="text-center text-muted py-4">Nothing new right now.</div>
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
        width: 46px; height: 46px; min-width: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .stat-label { font-size: 0.78rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #111827; }
    .activity-feed { max-height: 300px; overflow-y: auto; }
    .activity-item { display: flex; gap: 0.7rem; padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9; }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot { width: 8px; height: 8px; min-width: 8px; border-radius: 50%; background: #4f46e5; margin-top: 6px; }
    .activity-title { font-weight: 600; font-size: 0.85rem; color: #1f2937; }
    .activity-msg { font-size: 0.8rem; color: #6b7280; }
    .activity-time { font-size: 0.72rem; color: #9ca3af; margin-top: 2px; }
</style>
