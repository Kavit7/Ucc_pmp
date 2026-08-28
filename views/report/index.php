<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var float $totalCollected */
/** @var float $totalPending */
/** @var int $overdueCount */
/** @var int $totalProperties */
/** @var int $activeLeases */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4 body">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Total Collected</h6>
                    <h3>TZS <?= number_format($totalCollected, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Pending Revenue</h6>
                    <h3>TZS <?= number_format($totalPending, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Overdue Bills</h6>
                    <h3><?= $overdueCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Active Leases</h6>
                    <h3><?= $activeLeases ?> / <?= $totalProperties ?> properties</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <a href="<?= Url::to(['report/revenue']) ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>Revenue Report</h5>
                        <p class="text-muted mb-0">Bills by status, filterable by date range, with collected/pending totals.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= Url::to(['report/occupancy']) ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-building me-2"></i>Occupancy Report</h5>
                        <p class="text-muted mb-0">Properties broken down by status and usage type.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= Url::to(['report/leases']) ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-file-signature me-2"></i>Lease Report</h5>
                        <p class="text-muted mb-0">All leases with tenant, property, rent and status.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
