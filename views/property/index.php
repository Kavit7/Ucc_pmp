<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\helpers\ArrayHelper;

/** @var int $totalProperties */
/** @var int $occupiedCount */
$vacantCount = max(0, $totalProperties - $occupiedCount);
?>

<div class="container-fluid mt-4 fintech-page">

    <!-- Page header -->
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fintech-title mb-1">Properties</h3>
            <p class="fintech-subtitle mb-0">Manage your property portfolio</p>
        </div>
        <a href="<?= Url::to(['property/create']) ?>" class="btn btn-add-property">
            <i class="fas fa-plus me-1"></i> Add New Property
        </a>
    </div>

    <!-- Stat tiles -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-tile">
                <div class="stat-icon stat-icon-indigo"><i class="fas fa-building"></i></div>
                <div>
                    <div class="stat-value"><?= (int) $totalProperties ?></div>
                    <div class="stat-label">Total Properties</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-tile">
                <div class="stat-icon stat-icon-green"><i class="fas fa-door-closed"></i></div>
                <div>
                    <div class="stat-value"><?= (int) $occupiedCount ?></div>
                    <div class="stat-label">Occupied</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-tile">
                <div class="stat-icon stat-icon-amber"><i class="fas fa-door-open"></i></div>
                <div>
                    <div class="stat-value"><?= (int) $vacantCount ?></div>
                    <div class="stat-label">Vacant</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search / filter -->
    <div class="fintech-card p-3 mb-4">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' => ['class' => 'row g-2 align-items-center'],
    ]); ?>

    <div class="col-md-4">
        <div class="input-icon-wrap">
            <i class="fas fa-magnifying-glass"></i>
            <?= $form->field($searchModel, 'property_name', ['options' => ['class' => '']])->textInput(['placeholder' => 'Search by name', 'class' => 'styled-input'])->label(false) ?>
        </div>
    </div>

    <div class="col-md-3">
        <?= $form->field($searchModel, 'ownership_type_id')->dropDownList(
            $childOwner,
            ['prompt' => 'Ownership type', 'class' => 'form-select styled-select']
        )->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($searchModel, 'property_status_id')->dropDownList($childStatus, [
            'prompt' => 'Status',
            'class' => 'form-select styled-select',
        ])->label(false) ?>
    </div>

    <div class="col-md-2 d-flex gap-2">
        <?= Html::submitButton('Filter', ['class' => 'btn btn-add-property flex-fill']) ?>
        <?= Html::a('<i class="fas fa-rotate-left"></i>', ['index'], ['class' => 'btn btn-reset-filter', 'title' => 'Reset']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'row'],
        'itemOptions' => ['class' => 'col-lg-4 col-md-6 col-sm-12 mb-4'],
        'itemView' => function ($model) {
            $priceModel = $model->propertyPrice[0] ?? null;
            $priceValue = $priceModel ? $priceModel->unit_amount : null;

            $image = $model->document_url
                ? '<img src="' . Yii::getAlias('@web/' . $model->document_url) . '" alt="Property Image" class="property-img-sm">'
                : '<div class="no-image-sm d-flex align-items-center justify-content-center"><i class="fas fa-image"></i></div>';

            $statusName = $model->propertyStatus->list_Name ?? 'Unknown';
            $statusSlug = strtolower(str_replace(' ', '-', $statusName));

            return '
                <div class="fintech-card property-card-fintech">
                    <div class="property-img-wrapper">
                        ' . $image . '
                        <span class="status-pill status-pill-' . Html::encode($statusSlug) . '">' . Html::encode($statusName) . '</span>
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1 property-title">' . Html::encode($model->property_name) . '</h6>
                        <p class="property-location small mb-3"><i class="fas fa-location-dot me-1"></i>' . Html::encode($model->street->street_name ?? 'No location') . '</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="label-text small">Price</span>
                            <span class="price-text">' . ($priceValue !== null ? Html::encode(number_format($priceValue)) . ' <span class="price-currency">TZS</span>' : '<span class="text-muted small">Not set</span>') . '</span>
                        </div>
                        <div class="d-flex gap-2 icon-toolbar">
                            <a href="' . Url::to(['property/document', 'id' => $model->id]) . '" class="icon-btn icon-btn-view" title="View details"><i class="fas fa-eye"></i></a>
                            <a href="' . Url::to(['property/update', 'id' => $model->id]) . '" class="icon-btn icon-btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                            ' . Html::a('<i class="fas fa-trash"></i>', ['property/delete', 'id' => $model->id], [
                                'class' => 'icon-btn icon-btn-delete',
                                'title' => 'Delete',
                                'data-method' => 'post',
                                'data-confirm' => 'Delete this property? This only works if it has no lease history.',
                            ]) . '
                        </div>
                    </div>
                </div>';
        },
        'emptyText' => '<div class="fintech-card p-5 text-center text-muted"><i class="fas fa-building-circle-xmark fs-2 mb-2 d-block text-muted"></i>No properties match this filter.</div>',
        'layout' => "{items}\n<div class='col-12 mt-3'>{pager}</div>",
        'pager' => [
            'class' => \yii\widgets\LinkPager::class,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkOptions' => ['class' => 'page-link'],
            'prevPageLabel' => '&laquo',
            'nextPageCssClass' => 'active',
            'disabledPageCssClass' => 'disabled',
        ],
    ]) ?>
</div>

<?php $this->registerCss("
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

.fintech-page { background: #f4f6fb; border-radius: 16px; padding: 1.5rem; }
.fintech-title { font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
.fintech-subtitle { color: #64748b; font-size: 0.9rem; }

.fintech-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eef0f4;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

/* Stat tiles */
.stat-tile {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eef0f4;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.9rem;
    height: 100%;
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}
.stat-tile:hover { box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); transform: translateY(-2px); }
.stat-icon {
    width: 46px; height: 46px; min-width: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.stat-icon-indigo { background: #eef2ff; color: #4f46e5; }
.stat-icon-green { background: #ecfdf5; color: #10b981; }
.stat-icon-amber { background: #fffbeb; color: #f59e0b; }
.stat-value { font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1; }
.stat-label { font-size: 0.78rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }

/* Search bar */
.input-icon-wrap { position: relative; }
.input-icon-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.85rem; }
.input-icon-wrap .styled-input { padding-left: 2.4rem; }
.btn-reset-filter {
    background: #f1f5f9; color: #475569; border: none; border-radius: 10px; padding: 0 0.9rem;
}
.btn-reset-filter:hover { background: #e2e8f0; color: #1e293b; }

/* Property cards */
.property-card-fintech { overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; height: 100%; }
.property-card-fintech:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(79, 70, 229, 0.14); }
.property-img-wrapper { position: relative; height: 180px; overflow: hidden; background: linear-gradient(135deg, #e0e7ff, #ddd6fe); }
.property-img-sm { width: 100%; height: 100%; object-fit: cover; }
.no-image-sm { height: 100%; color: #818cf8; font-size: 2rem; }
.status-pill {
    position: absolute; top: 0.7rem; left: 0.7rem;
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;
    padding: 0.28rem 0.65rem; border-radius: 999px; color: #fff;
    background: #64748b;
}
.status-pill-active, .status-pill-available { background: #16a34a; }
.status-pill-occupied, .status-pill-rented { background: #4f46e5; }
.status-pill-maintenance, .status-pill-under-maintenance, .status-pill-under-maintainance { background: #f59e0b; }
.status-pill-inactive, .status-pill-unavailable { background: #dc2626; }

.property-title { color: #0f172a; }
.property-location { color: #6b7280; }
.label-text { color: #374151; }
.price-text { color: #0f172a; font-weight: 700; font-variant-numeric: tabular-nums; }
.price-currency { font-size: 0.72rem; font-weight: 600; color: #9ca3af; }

.icon-toolbar { border-top: 1px solid #f1f5f9; padding-top: 0.75rem; }
.icon-btn {
    flex: 1; display: flex; align-items: center; justify-content: center;
    height: 38px; border-radius: 10px; text-decoration: none; font-size: 0.9rem;
    transition: all 0.18s ease; border: none;
}
.icon-btn-view { background: #eef2ff; color: #4f46e5; }
.icon-btn-view:hover { background: #4f46e5; color: #fff; }
.icon-btn-edit { background: #ecfdf5; color: #10b981; }
.icon-btn-edit:hover { background: #10b981; color: #fff; }
.icon-btn-delete { background: #fef2f2; color: #dc2626; }
.icon-btn-delete:hover { background: #dc2626; color: #fff; }

/* Add-property button */
.btn-add-property {
    background-color: #4f46e5; color: #ffffff; border-radius: 10px;
    padding: 0.6rem 1.2rem; font-weight: 600; text-decoration: none;
    transition: background-color 0.2s ease; border: none;
}
.btn-add-property:hover { background-color: #4338ca; color: #ffffff; }

/* Pagination */
.pagination { gap: 0.3rem; }
.pagination .page-link { background-color: #fff; color: #4f46e5; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 12px; transition: all 0.2s; }
.pagination .page-link:hover { background-color: #eef2ff; color: #4338ca; }
.pagination .active .page-link { background-color: #4f46e5; border-color: #4f46e5; color: #fff; }
.pagination .disabled .page-link { background-color: #f8fafc; color: #cbd5e1; pointer-events: none; }

.styled-input, .styled-select, .styled-textarea {
    width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border-color);
    border-radius: 10px; color: var(--dark-text); font-size: 0.95rem;
    transition: all 0.2s ease; background-color: var(--light-bg);
}
.styled-input:focus, .styled-select:focus, .styled-textarea:focus {
    outline: none; border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12); background-color: #ffffff;
}
.styled-textarea { min-height: 120px; resize: vertical; }
"); ?>
