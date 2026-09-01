<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $totalAvailable */

$this->title = 'Available Properties';
?>
<style>
    .hero {
        background: linear-gradient(135deg, #1e1030 0%, #3730a3 55%, #4f46e5 100%);
        color: #fff;
        padding: 3.5rem 1.5rem 4.5rem;
        text-align: center;
    }
    .hero h2 {
        font-size: clamp(1.6rem, 3.2vw, 2.4rem);
        font-weight: 800;
        margin-bottom: 0.6rem;
        letter-spacing: -0.01em;
    }
    .hero p {
        color: #e0e7ff;
        font-size: 1.05rem;
        max-width: 560px;
        margin: 0 auto;
    }
    .hero .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.55rem 1.25rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.92rem;
        backdrop-filter: blur(4px);
    }

    .listing-wrap {
        max-width: 1180px;
        margin: -2.75rem auto 0;
        padding: 0 1.5rem 2rem;
    }

    .property-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.06);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #eef0f4;
    }
    .property-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(79, 70, 229, 0.18);
    }

    .property-photo {
        position: relative;
        height: 190px;
        overflow: hidden;
        background: linear-gradient(135deg, #e0e7ff, #ddd6fe);
    }
    .property-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .property-photo .no-photo {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 2.5rem;
    }
    .property-photo .type-badge {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        background: rgba(15, 23, 42, 0.72);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        backdrop-filter: blur(2px);
    }

    .property-body {
        padding: 1.15rem 1.25rem 1.25rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .property-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .property-location {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .property-price {
        font-size: 1.15rem;
        font-weight: 800;
        color: #4f46e5;
        margin-bottom: 1rem;
    }
    .property-price span {
        font-size: 0.78rem;
        font-weight: 500;
        color: #94a3b8;
    }
    .btn-inquire {
        margin-top: auto;
        background: #4f46e5;
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.6rem;
        transition: background 0.2s ease;
    }
    .btn-inquire:hover { background: #4338ca; }

    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #64748b;
    }
    .empty-state i { font-size: 2.5rem; color: #c7d2fe; margin-bottom: 1rem; }

    .pagination .page-link {
        border-radius: 8px;
        margin: 0 3px;
        color: #4f46e5;
        border-color: #e5e7eb;
    }
    .pagination .active .page-link {
        background: #4f46e5;
        border-color: #4f46e5;
    }
</style>

<div class="hero">
    <h2>Find Your Next Home</h2>
    <p>Browse our current selection of available properties and send an inquiry directly - no account or sign-up needed.</p>
    <div class="stat-pill"><i class="fas fa-key"></i> <?= (int) $totalAvailable ?> <?= $totalAvailable === 1 ? 'property' : 'properties' ?> available now</div>
</div>

<div class="listing-wrap">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['tag' => 'div', 'class' => 'row g-4'],
        'itemOptions' => ['tag' => 'div', 'class' => 'col-lg-4 col-md-6'],
        'itemView' => function ($model) {
            $photo = $model->photos[0]->photo_url ?? null;
            $image = $photo
                ? '<img src="' . Html::encode(Url::to('@web/' . $photo)) . '" alt="' . Html::encode($model->property_name) . '">'
                : '<div class="no-photo"><i class="fas fa-image"></i></div>';

            $typeBadge = $model->propertyType->list_Name ?? $model->usageType->list_Name ?? null;

            $priceModel = $model->propertyPrice[0] ?? null;
            $price = $priceModel
                ? 'TZS ' . number_format($priceModel->unit_amount, 0) . ' <span>/ term</span>'
                : '<span>Contact for price</span>';

            $location = $model->street->street_name ?? null;

            return '
                <div class="property-card">
                    <div class="property-photo">
                        ' . $image . '
                        ' . ($typeBadge ? '<span class="type-badge">' . Html::encode($typeBadge) . '</span>' : '') . '
                    </div>
                    <div class="property-body">
                        <div class="property-title">' . Html::encode($model->property_name) . '</div>
                        <div class="property-location"><i class="fas fa-location-dot"></i> ' . ($location ? Html::encode($location) : 'Location on request') . '</div>
                        <div class="property-price">' . $price . '</div>
                        <button type="button" class="btn btn-inquire" data-bs-toggle="modal" data-bs-target="#inquireModal" data-property-id="' . $model->id . '" data-property-name="' . Html::encode($model->property_name) . '">
                            <i class="fas fa-paper-plane me-1"></i> Inquire Now
                        </button>
                    </div>
                </div>';
        },
        'emptyText' => '<div class="empty-state"><i class="fas fa-house-circle-xmark d-block"></i>No properties are currently available.<br>Please check back soon.</div>',
        'layout' => "{items}\n<div class='mt-4 d-flex justify-content-center'>{pager}</div>",
    ]) ?>
</div>

<!-- Inquiry modal -->
<div class="modal fade" id="inquireModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <form method="post" action="<?= Url::to(['public-listing/inquire']) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <?= Html::hiddenInput('PropertyInquiry[property_id]', '', ['id' => 'inquire-property-id']) ?>
                <!-- Honeypot: hidden from real users via CSS, bots that autofill forms tend to fill it in -->
                <div style="position:absolute; left:-9999px;">
                    <label>Website</label>
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="modal-header" style="background:linear-gradient(135deg,#1e1030,#4f46e5); color:#fff; border:none;">
                    <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Inquire about <span id="inquire-property-name"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Your name</label>
                        <?= Html::textInput('PropertyInquiry[name]', '', ['class' => 'form-control', 'required' => true, 'placeholder' => 'e.g. Jane Doe']) ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <?= Html::input('email', 'PropertyInquiry[email]', '', ['class' => 'form-control', 'required' => true, 'placeholder' => 'you@example.com']) ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone (optional)</label>
                        <?= Html::textInput('PropertyInquiry[phone]', '', ['class' => 'form-control', 'placeholder' => '+255 ...']) ?>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Message (optional)</label>
                        <?= Html::textarea('PropertyInquiry[message]', '', ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Any questions or preferred viewing time?']) ?>
                    </div>
                </div>
                <div class="modal-footer" style="border:none; padding:0 1.5rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-inquire rounded-pill px-4" style="margin-top:0;">Send Inquiry</button>
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
