<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Journal';
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3 flex-wrap gap-2"
        style="background-color:#ffffff; border-radius:8px;">
        <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>
        <?= Html::a('<i class="fas fa-scale-balanced me-1"></i> Trial Balance', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php foreach ($dataProvider->getModels() as $entry): ?>
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between flex-wrap">
                    <div>
                        <span class="fw-semibold"><?= Html::encode($entry->description) ?></span>
                        <span class="text-muted small ms-2"><?= Html::encode($entry->entry_date) ?></span>
                    </div>
                    <span class="text-muted small"><?= Html::encode($entry->creator->full_name ?? 'System') ?></span>
                </div>
                <table class="table table-sm mb-0 mt-2">
                    <tbody>
                        <?php foreach ($entry->lines as $line): ?>
                            <tr>
                                <td class="border-0 ps-3" style="width: 60%;"><?= Html::encode($line->account->code . ' - ' . $line->account->name) ?></td>
                                <td class="border-0 text-end"><?= $line->debit > 0 ? number_format($line->debit, 2) : '' ?></td>
                                <td class="border-0 text-end"><?= $line->credit > 0 ? number_format($line->credit, 2) : '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($dataProvider->getCount() === 0): ?>
        <div class="text-center text-muted py-5">No journal entries yet.</div>
    <?php endif; ?>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>
