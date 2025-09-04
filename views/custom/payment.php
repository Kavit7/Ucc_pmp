<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $payments app\models\Bill[] */

$this->title = 'Payments';
?>
<div class="payment-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($payments)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lease No.</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= $payment->id ?></td>

                    <!-- Display Lease number instead of UUID -->
                    <td><?= $payment->lease->lease_number ?? $payment->lease->uuid ?? '-' ?></td>

                    <td><?= Yii::$app->formatter->asCurrency($payment->amount) ?></td>
                    <td><?= Yii::$app->formatter->asDate($payment->due_date) ?></td>
                    <td><?= $payment->paid_date ? Yii::$app->formatter->asDate($payment->paid_date) : '-' ?></td>

                    <td>
                        <?php 
                            switch ($payment->bill_status) {
                                case 'pending': echo '<span class="badge bg-warning">Pending</span>'; break;
                                case 'paid': echo '<span class="badge bg-success">Paid</span>'; break;
                                case 'overdue': echo '<span class="badge bg-danger">Overdue</span>'; break;
                                default: echo ucfirst($payment->bill_status);
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payments have been made yet.</p>
    <?php endif; ?>
</div>
