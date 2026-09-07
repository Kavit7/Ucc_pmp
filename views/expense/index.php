<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var float $totalExpenses */

$this->title = 'Expenses';
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <h3 class="mb-0">Expenses — Total: TZS <?= number_format($totalExpenses, 2) ?></h3>
        <?= Html::a('<i class="fas fa-plus me-1"></i> Log Expense', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Property</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount (TZS)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $expense): ?>
                    <tr>
                        <td><?= Html::encode(Yii::$app->formatter->asDate($expense->created_at, 'php:M j, Y')) ?></td>
                        <td><?= Html::encode($expense->property->property_name ?? '-') ?></td>
                        <td><?= Html::encode($expense->expense_type) ?></td>
                        <td><?= Html::encode($expense->description ?: '-') ?></td>
                        <td><?= number_format($expense->amount, 2) ?></td>
                        <td class="text-end">
                            <?= Html::beginForm(['delete', 'id' => $expense->id], 'post', ['style' => 'display:inline']) ?>
                                <?= Html::submitButton('<i class="fas fa-trash"></i>', [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'data' => ['confirm' => 'Remove this expense record? Its ledger entry stays as history.'],
                                ]) ?>
                            <?= Html::endForm() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($dataProvider->getCount() === 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No expenses logged yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>
