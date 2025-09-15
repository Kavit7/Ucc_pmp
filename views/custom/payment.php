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
                            switch (strtolower($payment->billStatus->list_Name)) {
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
        font-family: 'Inter', 'Roboto', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        
    }
</style>