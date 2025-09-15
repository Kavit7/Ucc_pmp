<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $bills app\models\Bill[] */

$this->title = 'Bills';
$this->registerCssFile('https://fonts.cdnfonts.com/css/sf-pro-display');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

// Prepare summary counts
$totalBills = count($bills);
$paidBills = count(array_filter($bills, fn($b) => $b->bill_status === 'paid'));
$pendingBills = count(array_filter($bills, function($bill) {
    return strtolower($bill->billStatus->list_Name) === 'pending';
}));
$overdueBills = count(array_filter($bills, fn($b) => $b->bill_status === 'overdue'));

// Styling
$this->registerCss("
     body {
        font-family: 'Inter', 'Roboto', sans-serif !important;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        
    }
.bill-container { position: relative; padding: 20px; }
.summary-cards { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
.card { flex: 1; text-align: center; padding: 15px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border: 1px solid #ddd; }
.card .count { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
.text-success { color: #27ae60; }
.text-warning { color: #f39c12; }
.text-danger { color: #c0392b; }
.filters { text-align: center; margin-bottom: 30px; font-size: 16px; font-weight: bold; }
.filters a { margin: 0 12px; color: black; text-decoration: none; font-weight: bold; cursor: pointer; }
.filters a.active { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 3px; }
.bill-table { background: #fff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); overflow-x: auto; }
.bill-table table { width: 100%; border-collapse: collapse; }
.bill-table th, .bill-table td { padding: 10px; border: 1px solid #ddd; color: black; vertical-align: middle; }
.bill-table th { background: #f1f1f1; text-align: left; }
.badge { font-weight: 600; border-radius: 0.25rem; padding: 0.25em 0.5em; border: 1px solid; }
.btn-danger { transition: background-color 0.3s ease; }
.btn-danger:hover { background-color: #c0392b; border-color: #c0392b; }
.no-bills { text-align: center; padding: 40px; font-size: 18px; color: #777; }
");

// Determine current filter
$currentFilter = Yii::$app->request->get('filter', 'all');

// Filter bills for table
$filteredBills = array_filter($bills, function($b) use ($currentFilter) {
    return match($currentFilter) {
        'paid' => $b->bill_status === 'paid',
        'unpaid' => in_array($b->bill_status, ['pending','overdue']),
        default => true
    };
});
?>

<div class="content bill-container">

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <div class="count"><?= $totalBills ?></div>
            <div>Total Bills</div>
        </div>
        <div class="card">
            <div class="count text-success"><?= $paidBills ?></div>
            <div>Paid Bills</div>
        </div>
        <div class="card">
            <div class="count text-warning"><?= $pendingBills ?></div>
            <div>Pending Bills</div>
        </div>
        <div class="card">
            <div class="count text-danger"><?= $overdueBills ?></div>
            <div>Overdue Bills</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <?= Html::a('All Bills', '#', ['class' => $currentFilter=='all'?'active':'', 'data-filter'=>'all']) ?>
        <?= Html::a('Paid Bills', '#', ['class' => $currentFilter=='paid'?'active':'', 'data-filter'=>'paid']) ?>
        <?= Html::a('Unpaid Bills', '#', ['class' => $currentFilter=='unpaid'?'active':'', 'data-filter'=>'unpaid']) ?>
    </div>

    <!-- Bills Table -->
    <div class="bill-table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lease No.</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Paid Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bill-tbody">
                <?php if(empty($filteredBills)): ?>
                    <tr><td colspan="7" class="no-bills">No bills found.</td></tr>
                <?php else: ?>
                    <?php foreach ($filteredBills as $bill): ?>
                        <tr id="bill-row-<?= $bill->id ?>" data-status="<?= $bill->bill_status ?>">
                            <td><?= $bill->id ?></td>
                            <td><?= Html::encode($bill->lease->lease_number ?? $bill->lease->uuid) ?></td>
                            <td><?= '$'.number_format($bill->amount,2) ?></td>
                            <td><?= Yii::$app->formatter->asDate($bill->due_date) ?></td>
                            <td>
                                <?php
                                $statusClass = match(strtolower($bill->billStatus->list_Name)){
                                    'paid' => 'text-success border-success',
                                    'pending' => 'text-warning border-warning',
                                    'overdue' => 'text-danger border-danger',
                                    default => 'text-secondary border-secondary'
                                };
                                $statusLabel = ucfirst($bill->billStatus->list_Name);
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td><?= $bill->paid_date ? Yii::$app->formatter->asDate($bill->paid_date) : '-' ?></td>
                            <td>
                                <?= Html::button('Delete', [
                                    'class' => 'btn btn-danger btn-sm delete-bill-btn',
                                    'data-id'=>$bill->id,
                                    'data-url'=>Url::to(['custom/delete-bill','id'=>$bill->id])
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JS for filtering & delete -->
<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends'=>[\yii\web\JqueryAsset::class]
]);

$js = <<<JS
// Filter click
$('.filters a').on('click', function(e){
    e.preventDefault();
    var filter = $(this).data('filter');
    $('.filters a').removeClass('active');
    $(this).addClass('active');

    $('#bill-tbody tr').each(function(){
        var status = $(this).data('status');
        if(filter==='all'){
            $(this).show();
        } else if(filter==='paid' && status==='paid'){
            $(this).show();
        } else if(filter==='unpaid' && (status==='pending'||status==='overdue')){
            $(this).show();
        } else{
            $(this).hide();
        }
    });
});

// Delete button
$('.delete-bill-btn').on('click', function(){
    var btn = $(this);
    var url = btn.data('url');
    var rowId = '#bill-row-'+btn.data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the bill!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result)=>{
        if(result.isConfirmed){
            $.ajax({
                url: url,
                type: 'POST',
                success: function(response){
                    if(response.status==='success'){
                        Swal.fire('Deleted!', response.message,'success');
                        $(rowId).remove();
                    } else {
                        Swal.fire('Error', response.message || 'Failed to delete bill.','error');
                    }
                },
                error:function(){
                    Swal.fire('Error','Something went wrong.','error');
                }
            });
        }
    });
});
JS;

$this->registerJs($js);
?>