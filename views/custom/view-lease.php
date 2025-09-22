<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $leases app\models\Lease[] */

$this->title = 'Tenant Leases';
?>

<div class="lease-view">

    <?php if (!empty($leases)): ?>
        <div class="shadow-sm rounded-4 p-4 bg-white">
            <div class="mb-4 border-bottom pb-3">
                <h3 class="fw-bold text-dark"><?= Html::encode($this->title) ?></h3>
                        <h5 class="text-center">Search by date</h5>
                <!-- Filter Inputs with Labels -->
                <div class="d-flex gap-3 mt-3">
                
                    <div class="d-flex flex-column col-6">
                        <label for="leaseStartDate" class="form-label fw-semibold">Start Date</label>
                        <input type="date" id="leaseStartDate" class="form-control">
                    </div>
                    <div class="d-flex flex-column col-6">
                        <label for="leaseEndDate" class="form-label fw-semibold">End Date</label>
                        <input type="date" id="leaseEndDate" class="form-control">
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row fw-semibold text-secondary small mb-2 border-bottom pb-2">
                    <div class="col-3">Customer</div>
                    <div class="col-2">Property Details</div>
                    <div class="col-3">Date</div>
                    <div class="col-2">Status</div>
                    <div class="col-2">Action</div>
                </div>

                <div class="row mb-4">
                    <div class="col-3">
                        <div class="fw-semibold text-dark">Name: <span class="text-primary"><?= $tname->full_name ?></span></div>
                        <div class="fw-semibold text-dark">Email: <span class="text-muted"><?= $tname->email ?></span></div>
                    </div>
                </div>
            </div>

            <?php foreach ($leases as $index => $lease): ?>
                <div class="container-fluid lease-row">
                    <div class="row align-items-center border rounded-3 p-3 mb-3 bg-light shadow-sm">
                        <div class="col-3">
                            <div class="fw-semibold text-dark">Contract No: <span class="text-secondary"><?= $index + 1 ?></span></div>
                        </div>
                        <div class="col-2">
                            <div class="fw-bold"><?= $lease->property->property_name ?? 'N/A' ?></div>
                            <div class="small text-muted"><?= $lease->property->description ?? 'N/A' ?></div>
                            <div class="small  text-success"><?= $lease->propertyPrice ? 'TZS:' . number_format($lease->propertyPrice->unit_amount, 2) : 'N/A' ?></div>
                        </div>
                        <div class="col-3 lease-dates" data-start="<?= $lease->lease_start_date ?>" data-end="<?= $lease->lease_end_date ?>">
                            <?php
                            $statusName = $lease->statusLabel->list_Name ?? 'Unknown';
                            $statusClass = match ($statusName) {
                                'Active' => 'bg-success text-white',
                                'Pending' => 'bg-primary text-white',
                                'Expired' => 'bg-danger text-white',
                                default => 'bg-secondary text-white'
                            };
                            $daysLeftText = '-';
                            if ($lease->lease_start_date && $lease->lease_end_date) {
                                $start = new DateTime($lease->lease_start_date);
                                $end = new DateTime($lease->lease_end_date);
                                $today = new DateTime();
                                if ($end >= $today) {
                                    $daysLeft = $today->diff($end)->days;
                                    $daysLeftText = "<span class='text-success fw-semibold'>$daysLeft days left</span>";
                                } else {
                                    $daysOver = $end->diff($today)->days;
                                    $daysLeftText = "<span class='text-danger fw-semibold'>Expired $daysOver days ago</span>";
                                }
                            }
                            ?>
                            <div class="small text-dark"><?= Html::encode($lease->lease_start_date ?? '-') ?> → <?= Html::encode($lease->lease_end_date ?? '-') ?></div>
                            <div class="small"><?= $daysLeftText ?></div>
                        </div>

                        <div class="col-2">
                            <span class="badge rounded-pill px-3 py-2 <?= $statusClass ?>"><?= Html::encode($statusName) ?></span>
                        </div>
                        <div class="col-2 d-flex gap-2">
                            <?= Html::a('Re-new', ['renew', 'id' => $lease->id], ['class' => 'btn btn-outline-primary btn-sm px-3']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info shadow-sm">No leases found for this tenant.</div>
    <?php endif; ?>

    <div class="mt-4">
        <?= Html::a('Cancel', 'leases', ['class' => 'btn btn-dark rounded-pill px-4']) ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function filterLeases() {
    var startDateInput = $('#leaseStartDate').val();
    var endDateInput = $('#leaseEndDate').val();

    var startDate = startDateInput ? new Date(startDateInput) : null;
    var endDate = endDateInput ? new Date(endDateInput) : null;

    $('.lease-row').each(function() {
        var leaseStart = $(this).find('.lease-dates').data('start');
        var leaseEnd = $(this).find('.lease-dates').data('end');

        if (!leaseStart || !leaseEnd) {
            $(this).toggle(false);
            return;
        }

        var leaseStartDate = new Date(leaseStart);
        var leaseEndDate = new Date(leaseEnd);

        var show = true;

        // Show only if lease overlaps with filter
        if (startDate && leaseEndDate < startDate) {
            show = false;
        }
        if (endDate && leaseStartDate > endDate) {
            show = false;
        }

        $(this).toggle(show);
    });
}

// Bind change events
$('#leaseStartDate, #leaseEndDate').on('change', filterLeases);
JS);
?>

<style>
body {
    font-family: 'Inter', 'Roboto', sans-serif !important;
    background: #f4f6f9;
}

.lease-view h3 {
    color: #111827;
}

.row.bg-light {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.row.bg-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
}

@media (max-width: 768px) {
    .row>div {
        margin-bottom: 10px;
    }
}

.form-label {
    font-weight: 600;
    font-size: 0.9rem;
}
</style>
