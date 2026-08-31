<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string[] $modelOptions */
/** @var string|null $selectedModel */
/** @var string|null $selectedAction */

$this->title = 'Audit Log';
?>
<div class="container mt-4 custom-container">
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <form method="get" class="row g-2 mb-4 bg-white p-3 rounded">
        <div class="col-md-4">
            <select name="model_name" class="form-select" onchange="this.form.submit()">
                <option value="">All models</option>
                <?php foreach ($modelOptions as $option): ?>
                    <option value="<?= Html::encode($option) ?>" <?= $option === $selectedModel ? 'selected' : '' ?>>
                        <?= Html::encode(str_replace('app\\models\\', '', $option)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="action" class="form-select" onchange="this.form.submit()">
                <option value="">All actions</option>
                <?php foreach (['create', 'update', 'delete'] as $action): ?>
                    <option value="<?= $action ?>" <?= $action === $selectedAction ? 'selected' : '' ?>>
                        <?= ucfirst($action) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </form>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($model) {
            $badgeClass = [
                'create' => 'bg-success',
                'update' => 'bg-primary',
                'delete' => 'bg-danger',
            ][$model->action] ?? 'bg-secondary';

            $changes = $model->getChangesDecoded();
            $summary = [];
            if ($model->action === 'update') {
                foreach ($changes as $attr => $vals) {
                    $summary[] = Html::encode($attr) . ': <span class="text-muted">' . Html::encode((string) ($vals['old'] ?? '')) . '</span> &rarr; ' . Html::encode((string) ($vals['new'] ?? ''));
                }
            } else {
                $summary[] = count($changes) . ' field(s) recorded';
            }

            return '
                <div class="card mb-2 border-0 shadow-sm">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="badge ' . $badgeClass . ' me-2">' . strtoupper(Html::encode($model->action)) . '</span>
                                <strong>' . Html::encode(str_replace('app\\models\\', '', $model->model_name)) . '</strong>
                                <span class="text-muted">#' . Html::encode($model->model_id) . '</span>
                            </div>
                            <div class="text-muted small">
                                ' . Html::encode($model->user->full_name ?? 'System') . ' &middot; ' . Html::encode($model->created_at) . '
                            </div>
                        </div>
                        <div class="small mt-1">' . implode('<br>', array_slice($summary, 0, 5)) . (count($summary) > 5 ? '<div class="text-muted">+' . (count($summary) - 5) . ' more</div>' : '') . '</div>
                    </div>
                </div>';
        },
        'layout' => "{items}\n<div class='mt-3'>{pager}</div>",
        'options' => ['tag' => 'div'],
    ]) ?>
</div>
