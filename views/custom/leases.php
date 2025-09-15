<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Leases'; 

// Register SweetAlert2 and jQuery
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

// Prepare summary counts
$totalLeases = count($leases);
$activeLeases = count(array_filter($leases, fn($l) => $l->statusLabel->list_Name === 'Active'));
$pendingLeases = count(array_filter($leases, fn($l) => $l->statusLabel->list_Name === 'Pending'));
$expiringLeases = count(array_filter($leases, function($l){
    if(!$l->lease_end_date) return false;
    $end = new DateTime($l->lease_end_date);
    $today = new DateTime();
    $diff = $today->diff($end)->days;
    return $diff <= 30 && $end >= $today;
}));

// Flash messages
$flashMessages = Yii::$app->session->getAllFlashes();
$flashJs = "";
foreach ($flashMessages as $type => $message) {
    $icon = $type === 'success' ? 'success' : ($type === 'error' ? 'error' : 'info');
    $flashJs .= "Swal.fire({icon: '$icon', title: '".Html::encode($message)."'});\n";
}
?>

<div class="lease-index container my-5 .body">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text"><?= Html::encode($this->title) ?></h2>
        <?= Html::a('Create Lease', ['custom/create-lease'], ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-5 g-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-4 border border-dark rounded-3 summary-card">
                <div class="fs-3 fw-bold text-dark"><?= $totalLeases ?></div>
                <div class="text-secondary mt-1">Total Leases</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-4 border border-dark rounded-3 summary-card">
                <div class="fs-3 fw-bold text-dark"><?= $activeLeases ?></div>
                <div class="text-secondary mt-1">Active Leases</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-4 border border-dark rounded-3 summary-card">
                <div class="fs-3 fw-bold text-dark"><?= $expiringLeases ?></div>
                <div class="text-secondary mt-1">Expiring Soon</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-4 border border-dark rounded-3 summary-card">
                <div class="fs-3 fw-bold text-dark"><?= $pendingLeases ?></div>
                <div class="text-secondary mt-1">Pending Leases</div>
            </div>
        </div>
    </div>

    <!-- Lease Table -->
    <div class="table-responsive shadow-sm rounded-3 p-3 bg-white">
        <table class="table table-hover align-middle">
            <thead class="table-light text-uppercase text-secondary">
                <tr>
                    <th>ID</th>
                    <th>Property</th>
                    <th>Tenant</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Lease Period</th>
                    <th>Duration (Months)</th>
                    <th>Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($leases as $lease): ?>
                <?php 
                    $tenantName = $lease->tenant->fullname ?? $lease->tenant->username ?? $lease->tenant->email ?? 'N/A';
                    $statusName = $lease->statusLabel->list_Name ?? 'Unknown';
                    $statusClass = match($statusName){
                        'Active' => 'bg-success text-white',
                        'Pending' => 'bg-primary text-white',
                        'Expired' => 'bg-danger text-white',
                        default => 'bg-secondary text-white'
                    };
                    $duration = '-';
                    $daysLeftText = '-';
                    if($lease->lease_start_date && $lease->lease_end_date){
                        $start = new DateTime($lease->lease_start_date);
                        $end = new DateTime($lease->lease_end_date);
                        $duration = ($end->format('Y') - $start->format('Y')) * 12 + ($end->format('m') - $start->format('m'));

                        $today = new DateTime();
                        if($end >= $today){
                            $daysLeft = $today->diff($end)->days;
                            $daysLeftText = "<span class='text-success fw-semibold'>$daysLeft days left</span>";
                        } else {
                            $daysOver = $end->diff($today)->days;
                            $daysLeftText = "<span class='text-danger fw-semibold'>Expired $daysOver days ago</span>";
                        }
                    }
                ?>
                <tr>
                    <td class="fw-bold"><?= Html::encode($lease->id) ?></td>
                    <td><?= Html::encode($lease->property->property_name ?? 'N/A') ?></td>
                    <td><?= Html::encode($tenantName) ?></td>
                    <td><?= $lease->propertyPrice ? '$' . number_format($lease->propertyPrice->unit_amount, 2) : 'N/A' ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= $statusName ?></span></td>
                    <td>
                        <div><?= Html::encode($lease->lease_start_date ?? '-') ?> → <?= Html::encode($lease->lease_end_date ?? '-') ?></div>
                        <div class="small"><?= $daysLeftText ?></div>
                    </td>
                    <td><?= $duration ?></td>
                    <td>
                        <?php 
                        if($lease->lease_doc_url){
                            $filePath = Yii::getAlias('@webroot/' . $lease->lease_doc_url);
                            $fileUrl  = Yii::getAlias('@web/' . $lease->lease_doc_url);
                            if(file_exists($filePath)){
                                echo Html::a('View Doc', $fileUrl, ['target'=>'_blank', 'class'=>'text-decoration-none fw-semibold']);
                            } else {
                                echo '<span class="text-danger">Not Found</span>';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td>
                        <?= Html::a('View', ['custom/view-lease', 'id' => $lease->id], ['class'=>'me-3 text-decoration-none fw-semibold']) ?>
                        <?= Html::a('Delete', '#', [
                            'class' => 'delete-lease text-danger text-decoration-none fw-semibold',
                            'data-id' => $lease->id,
                            'data-url' => Url::to(['custom/delete-lease', 'id' => $lease->id])
                        ]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php
$js = <<<JS
$flashJs

// Delete lease confirmation
$('.delete-lease').on('click', function(e){
    e.preventDefault();
    var url = $(this).data('url');
    Swal.fire({
        title: 'Are you sure?',
        text: "This lease will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {_csrf: yii.getCsrfToken()}, function(response){
                if(response.status === 'success'){
                    Swal.fire('Deleted!', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', response.message || 'Failed to delete', 'error');
                }
            }).fail(function(){
                Swal.fire('Error','An error occurred while deleting.','error');
            });
        }
    });
});
JS;
$this->registerJs(new JsExpression($js));
?>

<style>
     :root {
        --primary: #4f46e5;
        --primary-dark: #4338ca;
        --secondary: #10b981;
        --light-bg: #f9fafb;
        --dark-text: #1f2937;
        --mid-text: #4b5563;
        --light-text: #6b7280;
        --border-color: #e5e7eb;
        --success: #10b981;
    }
     body {
        font-family: 'Inter', 'Roboto', sans-serif !important;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        
    }
/* Small screen adjustments */
@media (max-width: 576px) {
    .table th, .table td {
        white-space: nowrap;
        font-size: 0.85rem;
    }
}

/* Card hover effect */
.card.summary-card {
    background: transparent !important;
    transition: transform 0.2s, box-shadow 0.2s;
}
.card.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* Typography improvements */
.fw-semibold { font-weight: 600; }
.table td, .table th { vertical-align: middle; font-size: 0.95rem;color: var(--light-text); }


/* Add space between cards and table */
.table-responsive {
    margin-top: 40px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
}
</style>