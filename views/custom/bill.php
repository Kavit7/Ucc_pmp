<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $bills app\models\Bill[] */

$this->title = 'Bills';
?>
<div class="bill-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Lease No.</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Paid Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($bills as $bill): ?>
            <tr>
                <!-- Display bill ID or friendly number if implemented -->
                <td><?= $bill->id ?></td>  

                <!-- Display Lease number instead of raw UUID for friendliness -->
                <td><?= $bill->lease->lease_number ?? $bill->lease->uuid ?></td>  

                <td><?= Yii::$app->formatter->asCurrency($bill->amount) ?></td>
                <td><?= Yii::$app->formatter->asDate($bill->due_date) ?></td>

                <!-- Capitalize status for better look -->
                <td>
                    <?php 
                        switch ($bill->bill_status) {
                            case 'pending': echo '<span class="badge bg-warning">Pending</span>'; break;
                            case 'paid': echo '<span class="badge bg-success">Paid</span>'; break;
                            case 'overdue': echo '<span class="badge bg-danger">Overdue</span>'; break;
                            default: echo ucfirst($bill->bill_status);
                        }
                    ?>
                </td>

                <td><?= $bill->paid_date ? Yii::$app->formatter->asDate($bill->paid_date) : '-' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
