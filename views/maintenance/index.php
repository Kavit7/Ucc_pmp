<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $statusOptions */
/** @var string|null $statusId */
/** @var bool $isStaff */

$this->title = 'Maintenance Requests';
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>
        <div class="d-flex align-items-center gap-2">
            <form method="get" action="<?= Url::to(['maintenance/index']) ?>" class="d-flex">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    <?php foreach ($statusOptions as $id => $name): ?>
                        <option value="<?= $id ?>" <?= (string) $statusId === (string) $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?= Html::a('<i class="fas fa-plus me-1"></i> Report an Issue', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Issue</th>
                    <th>Property</th>
                    <?php if ($isStaff): ?><th>Reported By</th><?php endif; ?>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Reported</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $req): ?>
                    <?php
                    $statusName = strtolower($req->status->list_Name ?? '');
                    $statusClass = match ($statusName) {
                        'open' => 'text-danger border-danger',
                        'in progress' => 'text-warning border-warning',
                        'resolved', 'closed' => 'text-success border-success',
                        default => 'text-secondary border-secondary',
                    };
                    ?>
                    <tr>
                        <td><?= Html::encode($req->title) ?></td>
                        <td><?= Html::encode($req->property->property_name ?? '-') ?></td>
                        <?php if ($isStaff): ?><td><?= Html::encode($req->reportedBy->full_name ?? '-') ?></td><?php endif; ?>
                        <td><?= Html::encode($req->priority->list_Name ?? '-') ?></td>
                        <td><span class="badge <?= $statusClass ?>"><?= Html::encode($req->status->list_Name ?? '-') ?></span></td>
                        <td><?= Html::encode($req->assignedTo->full_name ?? 'Unassigned') ?></td>
                        <td><?= Yii::$app->formatter->asRelativeTime($req->created_at) ?></td>
                        <td><?= Html::a($isStaff ? 'Manage' : 'View', ['update', 'id' => $req->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($dataProvider->getCount() === 0): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">No maintenance requests yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>
