<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Available Properties';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Available Properties</h2>
        <p class="text-muted">Browse our currently vacant properties and send an inquiry - no account needed.</p>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['tag' => 'div', 'class' => 'row g-4'],
        'itemOptions' => ['tag' => 'div', 'class' => 'col-md-4'],
        'itemView' => function ($model) {
            $photo = $model->photos[0]->photo_url ?? $model->document_url ?? null;
            $image = $photo
                ? '<img src="' . Html::encode(Url::to('@web/' . $photo)) . '" class="card-img-top" style="height:200px;object-fit:cover;" alt="">'
                : '<div class="d-flex align-items-center justify-content-center bg-light" style="height:200px;color:#9ca3af;">No photo</div>';

            $priceModel = $model->propertyPrice[0] ?? null;
            $price = $priceModel ? 'TZS ' . number_format($priceModel->unit_amount, 2) : 'Contact for price';

            return '
                <div class="card h-100 shadow-sm border-0">
                    ' . $image . '
                    <div class="card-body">
                        <h5 class="card-title">' . Html::encode($model->property_name) . '</h5>
                        <p class="text-muted small mb-2">' . Html::encode($model->propertyType->list_Name ?? '') . '</p>
                        <p class="fw-semibold text-primary mb-3">' . $price . '</p>
                        <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#inquireModal" data-property-id="' . $model->id . '" data-property-name="' . Html::encode($model->property_name) . '">
                            Inquire
                        </button>
                    </div>
                </div>';
        },
        'emptyText' => 'No properties are currently available. Please check back soon.',
        'layout' => "{items}\n<div class='mt-4 d-flex justify-content-center'>{pager}</div>",
    ]) ?>
</div>

<!-- Inquiry modal -->
<div class="modal fade" id="inquireModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= Url::to(['public-listing/inquire']) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <?= Html::hiddenInput('PropertyInquiry[property_id]', '', ['id' => 'inquire-property-id']) ?>
                <!-- Honeypot: hidden from real users via CSS, bots that autofill forms tend to fill it in -->
                <div style="position:absolute; left:-9999px;">
                    <label>Website</label>
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="modal-header">
                    <h5 class="modal-title">Inquire about <span id="inquire-property-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Your name</label>
                        <?= Html::textInput('PropertyInquiry[name]', '', ['class' => 'form-control', 'required' => true]) ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <?= Html::input('email', 'PropertyInquiry[email]', '', ['class' => 'form-control', 'required' => true]) ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone (optional)</label>
                        <?= Html::textInput('PropertyInquiry[phone]', '', ['class' => 'form-control']) ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message (optional)</label>
                        <?= Html::textarea('PropertyInquiry[message]', '', ['class' => 'form-control', 'rows' => 3]) ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
document.getElementById('inquireModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('inquire-property-id').value = button.getAttribute('data-property-id');
    document.getElementById('inquire-property-name').textContent = button.getAttribute('data-property-name');
});
JS);
?>
