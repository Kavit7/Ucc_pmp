<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use app\models\ListSource;

/* @var $this yii\web\View */
/* @var $prices app\models\PropertyPrice[] */

$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

// Map price_type IDs to names from list_source table
$parent= ListSource::find()->where(['category'=>'Usage Type','parent_id'=>null])->one();
$priceTypeMap = ArrayHelper::map(
    ListSource::find()->where(['parent_id' => $parent])->all(),
    'id',
    'list_Name'
);

// Summary counts
$totalPrices = count($prices);
$usageTypeCounts = [];
foreach($prices as $p){
    $typeName = $priceTypeMap[$p->price_type] ?? 'Other';
    if(!isset($usageTypeCounts[$typeName])) $usageTypeCounts[$typeName] = 0;
    $usageTypeCounts[$typeName]++;
}
?>

<div class="property-price-index container my-4 .body">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Property Prices</h3>
        <?= Html::a('Add Property Price', ['property-price/save'], ['class' => 'btn btn-primary']) ?>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-3 border border-dark">
                <div class="fs-4 fw-bold"><?= $totalPrices ?></div>
                <div class="text-muted">Total Prices</div>
            </div>
        </div>
        <?php foreach($usageTypeCounts as $typeName => $count): ?>
            <div class="col-md-3">
                <div class="card shadow-sm text-center py-3 border border-dark">
                    <div class="fs-4 fw-bold"><?= $count ?></div>
                    <div class="text-muted mt-1"><?= Html::encode($typeName) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Prices Table -->
    <div class="table-responsive shadow-sm">
        <table class="table table-hover align-middle rounded-3">
            <thead class="table-light">
                <tr>
                    <th>Property</th>
                    <th>Type</th>
                    <th>Unit Amount</th>
                    <th>Period</th>
                    <th>Min Rent</th>
                    <th>Max Rent</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $price): ?>
                    <tr>
                        <td><?= $price->property ? Html::encode($price->property->property_name) : '-' ?></td>
                        <td>
                            <?= isset($priceTypeMap[$price->price_type]) 
                                ? Html::encode($priceTypeMap[$price->price_type]) 
                                : $price->price_type ?>
                        </td>
                        <td><?= number_format($price->unit_amount, 2) ?></td>
                        <td><?= Html::encode($price->period) ?></td>
                        <td><?= $price->min_monthly_rent ? number_format($price->min_monthly_rent, 2) : '-' ?></td>
                        <td><?= $price->max_monthly_rent ? number_format($price->max_monthly_rent, 2) : '-' ?></td>
                        <td><?= $price->created_at ?></td>
                        <td>
                            <?= Html::a('Edit', ['property-price/save', 'id' => $price->id], ['class' => 'btn text-primary btn-sm mb-1']) ?>
                            <?= Html::a('Delete', '#', [
                                'class' => 'btn text-danger btn-sm mb-1 delete-price',
                                'data-id' => $price->id,
                                'data-url' => Url::to(['property-price/delete', 'id' => $price->id])
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Styling -->
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
     .body {
        font-family: 'Inter', 'Roboto', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
    }
/* Cards hover effect */
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.2s;
}

/* Table styling */
.table {
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.table thead {
    background-color: #f8f9fa;
    font-weight: 600;
}
.table tbody tr:hover {
    background-color: #f1f3f5;
}

/* Buttons hover */
.btn-primary, .btn-danger {
    transition: background-color 0.3s ease;
}
.btn-primary:hover { background-color: #0d6efdcc; }
.btn-danger:hover { background-color: #c0392b; border-color: #c0392b; }
</style>

<!-- JS Delete with SweetAlert2 -->
<?php
$js = <<<JS
$('.delete-price').on('click', function(e){
    e.preventDefault();
    var url = $(this).data('url');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
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