<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $prices app\models\PropertyPrice[] */

$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class]
]);
?>

<div class="property-price-index" style="max-width: 100%; margin: 0 auto;">
    <h3 class="mb-3">Property Prices</h3>
    <p>
        <?= Html::a('Add Property Price', ['property-price/save'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Property</th>
                    <th>UUID</th>
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
                        <td><?= Html::encode($price->uuid) ?></td>
                        <td><?= Html::encode($price->price_type) ?></td>
                        <td><?= number_format($price->unit_amount, 2) ?></td>
                        <td><?= Html::encode($price->period) ?></td>
                        <td><?= $price->min_monthly_rent ? number_format($price->min_monthly_rent, 2) : '-' ?></td>
                        <td><?= $price->max_monthly_rent ? number_format($price->max_monthly_rent, 2) : '-' ?></td>
                        <td><?= $price->created_at ?></td>
                        <td>
                            <?= Html::a('Edit', ['property-price/save', 'id' => $price->id], ['class' => 'btn btn-primary btn-sm mb-1']) ?>
                            <?= Html::a('Delete', '#', [
                                'class' => 'btn btn-danger btn-sm mb-1 delete-price',
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
                    Swal.fire('Deleted!', response.message, 'success').then(()=> location.reload());
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
