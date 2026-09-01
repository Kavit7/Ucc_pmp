<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $totalAvailable */
/** @var int $totalOwned */
/** @var array $typeBreakdown */
/** @var string[] $regionNames */

$this->title = 'Available Properties';

// Build a friendly, data-driven welcome line describing the portfolio,
// e.g. "8 cars and 4 houses across Dodoma and Dar es Salaam."
$typeParts = [];
foreach ($typeBreakdown as $row) {
    $label = strtolower($row['type_name']);
    $label = (int) $row['total'] === 1 ? $label : $label . 's';
    $typeParts[] = (int) $row['total'] . ' ' . $label;
}
$typesSentence = '';
if (count($typeParts) === 1) {
    $typesSentence = $typeParts[0];
} elseif (count($typeParts) > 1) {
    $last = array_pop($typeParts);
    $typesSentence = implode(', ', $typeParts) . ' and ' . $last;
}

$prettyRegions = array_map(function ($name) {
    return Html::encode(ucwords(str_replace('-', ' ', $name)));
}, $regionNames);
$regionsSentence = '';
if (count($prettyRegions) === 1) {
    $regionsSentence = $prettyRegions[0];
} elseif (count($prettyRegions) > 1) {
    $lastRegion = array_pop($prettyRegions);
    $regionsSentence = implode(', ', $prettyRegions) . ' and ' . $lastRegion;
}

$welcomeLine = "We're proud to own and manage {$totalOwned} properties";
if ($typesSentence) {
    $welcomeLine .= " - {$typesSentence}";
}
if ($regionsSentence) {
    $welcomeLine .= ", across {$regionsSentence}";
}
$welcomeLine .= '.';

// Property details, keyed by id, for the "View Details" popup - built once
// here so the modal can be populated client-side without extra requests.
$propertyDetails = [];
foreach ($dataProvider->getModels() as $model) {
    $photo = $model->photos[0]->photo_url ?? null;
    $priceModel = $model->propertyPrice[0] ?? null;
    $propertyDetails[$model->id] = [
        'name' => $model->property_name,
        'image' => $photo ? Url::to('@web/' . $photo) : null,
        'type' => $model->propertyType->list_Name ?? $model->usageType->list_Name ?? null,
        'location' => $model->street->street_name ?? null,
        'price' => $priceModel ? 'TZS ' . number_format($priceModel->unit_amount, 0) : null,
        'description' => $model->description ?: 'No further description has been provided for this property yet - send an inquiry and we\'ll fill you in.',
    ];
}
?>
<style>
    /* ---------- Hero carousel ---------- */
    #heroCarousel .carousel-item {
        min-height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff;
        padding: 3rem 1.5rem;
    }
    .hero-slide-1 { background: linear-gradient(135deg, #1e1030 0%, #3730a3 55%, #4f46e5 100%); }
    .hero-slide-2 { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0ea5e9 100%); }
    .hero-slide-3 { background: linear-gradient(135deg, #1e1030 0%, #6d28d9 55%, #a855f7 100%); }

    .hero-content { max-width: 620px; animation: heroFadeUp 0.8s ease both; }
    .hero-content .hero-icon {
        width: 62px; height: 62px; border-radius: 16px;
        background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; margin: 0 auto 1.1rem;
        backdrop-filter: blur(4px);
    }
    .hero-content h2 {
        font-size: clamp(1.6rem, 3.2vw, 2.4rem);
        font-weight: 800;
        margin-bottom: 0.6rem;
        letter-spacing: -0.01em;
    }
    .hero-content p { color: #e0e7ff; font-size: 1.05rem; margin: 0 auto; }
    .hero .stat-pill {
        display: inline-flex; align-items: center; gap: 0.5rem;
        margin-top: 1.5rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.55rem 1.25rem; border-radius: 999px;
        font-weight: 600; font-size: 0.92rem;
        backdrop-filter: blur(4px);
    }

    #heroCarousel .carousel-indicators { margin-bottom: 0.5rem; }
    #heroCarousel .carousel-indicators [data-bs-target] {
        width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); border: none;
    }
    #heroCarousel .carousel-indicators .active { background: #fff; }
    #heroCarousel .carousel-control-prev,
    #heroCarousel .carousel-control-next { width: 5%; opacity: 0.6; }
    #heroCarousel .carousel-control-prev:hover,
    #heroCarousel .carousel-control-next:hover { opacity: 1; }

    @keyframes heroFadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ---------- Trust strip ---------- */
    .trust-strip {
        max-width: 1180px;
        margin: -2rem auto 0;
        padding: 0 1.5rem;
        position: relative;
        z-index: 2;
    }
    .trust-strip .row {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.09);
        padding: 1.1rem 0.5rem;
    }
    .trust-item {
        display: flex; align-items: center; justify-content: center; gap: 0.6rem;
        font-size: 0.85rem; font-weight: 600; color: #334155;
        padding: 0.4rem 0.5rem;
    }
    .trust-item i { color: #4f46e5; font-size: 1.05rem; }

    /* ---------- Listing grid ---------- */
    .listing-wrap { max-width: 1180px; margin: 0 auto; padding: 2.5rem 1.5rem 2rem; }
    .listing-heading { font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; }

    .property-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #eef0f4;
        opacity: 0;
        transform: translateY(24px);
    }
    .property-card.in-view {
        animation: cardFadeUp 0.55s ease forwards;
    }
    @keyframes cardFadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .property-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(79, 70, 229, 0.18);
    }

    .property-photo { position: relative; height: 190px; overflow: hidden; background: linear-gradient(135deg, #e0e7ff, #ddd6fe); }
    .property-photo img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
    .property-card:hover .property-photo img { transform: scale(1.08); }
    .property-photo .no-photo { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 2.5rem; }
    .property-photo .type-badge {
        position: absolute; top: 0.75rem; left: 0.75rem;
        background: rgba(15, 23, 42, 0.72); color: #fff;
        font-size: 0.72rem; font-weight: 600; padding: 0.3rem 0.7rem;
        border-radius: 999px; text-transform: uppercase; letter-spacing: 0.03em;
        backdrop-filter: blur(2px);
    }
    .property-photo .view-overlay {
        position: absolute; inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.25s ease;
        cursor: pointer;
    }
    .property-card:hover .view-overlay { opacity: 1; }
    .view-overlay span {
        color: #fff; font-weight: 600; font-size: 0.85rem;
        border: 1px solid rgba(255,255,255,0.6); border-radius: 999px;
        padding: 0.45rem 1.1rem;
    }

    .property-body { padding: 1.15rem 1.25rem 1.25rem; display: flex; flex-direction: column; flex: 1; }
    .property-title { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .property-location { color: #64748b; font-size: 0.85rem; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.35rem; }
    .property-price { font-size: 1.15rem; font-weight: 800; color: #4f46e5; margin-bottom: 1rem; }
    .property-price span { font-size: 0.78rem; font-weight: 500; color: #94a3b8; }

    .card-actions { margin-top: auto; display: flex; gap: 0.5rem; }
    .btn-inquire, .btn-details {
        border: none; font-weight: 600; border-radius: 10px; padding: 0.6rem;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .btn-inquire { flex: 1.3; background: #4f46e5; color: #fff; }
    .btn-inquire:hover { background: #4338ca; color: #fff; }
    .btn-details { flex: 1; background: #eef2ff; color: #4f46e5; }
    .btn-details:hover { background: #e0e7ff; color: #4338ca; }

    .empty-state { text-align: center; padding: 4rem 1rem; color: #64748b; }
    .empty-state i { font-size: 2.5rem; color: #c7d2fe; margin-bottom: 1rem; }

    .pagination .page-link { border-radius: 8px; margin: 0 3px; color: #4f46e5; border-color: #e5e7eb; }
    .pagination .active .page-link { background: #4f46e5; border-color: #4f46e5; }

    /* ---------- Modals ---------- */
    .modal-content { border-radius: 16px; border: none; overflow: hidden; }
    .modal-header-gradient { background: linear-gradient(135deg, #1e1030, #4f46e5); color: #fff; border: none; }
    #detailsModal .details-photo {
        height: 260px; background: linear-gradient(135deg, #e0e7ff, #ddd6fe);
        display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    #detailsModal .details-photo img { width: 100%; height: 100%; object-fit: cover; }
    #detailsModal .details-photo i { font-size: 3rem; color: #818cf8; }
</style>

<!-- Hero slideshow -->
<div id="heroCarousel" class="carousel slide hero" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="hero-slide-1 w-100">
                <div class="hero-content mx-auto">
                    <div class="hero-icon"><i class="fas fa-hand-sparkles"></i></div>
                    <h2>Welcome! We're glad you're here</h2>
                    <p><?= Html::encode($welcomeLine) ?> Have a look around - we'd love to help you find the right fit.</p>
                    <div class="stat-pill"><i class="fas fa-house-circle-check"></i> <?= (int) $totalAvailable ?> <?= $totalAvailable === 1 ? 'property' : 'properties' ?> available now</div>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="hero-slide-2 w-100">
                <div class="hero-content mx-auto">
                    <div class="hero-icon"><i class="fas fa-shield-halved"></i></div>
                    <h2>Verified &amp; Trusted Listings</h2>
                    <p>Every property is managed directly by our team - no third-party brokers, no surprise fees.</p>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="hero-slide-3 w-100">
                <div class="hero-content mx-auto">
                    <div class="hero-icon"><i class="fas fa-bolt"></i></div>
                    <h2>Fast, Direct Response</h2>
                    <p>Send an inquiry and hear back from our team quickly - no waiting rooms, no middlemen.</p>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
    <div class="carousel-indicators position-relative mb-0 mt-0" style="bottom:auto;">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
</div>

<!-- Trust strip -->
<div class="trust-strip">
    <div class="row g-2 text-center">
        <div class="col-6 col-md-3"><div class="trust-item"><i class="fas fa-circle-check"></i> Verified Listings</div></div>
        <div class="col-6 col-md-3"><div class="trust-item"><i class="fas fa-hand-holding-dollar"></i> No Hidden Fees</div></div>
        <div class="col-6 col-md-3"><div class="trust-item"><i class="fas fa-clock"></i> Fast Response</div></div>
        <div class="col-6 col-md-3"><div class="trust-item"><i class="fas fa-house-flag"></i> Wide Selection</div></div>
    </div>
</div>

<div class="listing-wrap">
    <h4 class="listing-heading">Available Now</h4>
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
                <div class="property-card reveal-on-scroll">
                    <div class="property-photo" data-bs-toggle="modal" data-bs-target="#detailsModal" data-property-id="' . $model->id . '">
                        ' . $image . '
                        ' . ($typeBadge ? '<span class="type-badge">' . Html::encode($typeBadge) . '</span>' : '') . '
                        <div class="view-overlay"><span><i class="fas fa-eye me-1"></i> View Details</span></div>
                    </div>
                    <div class="property-body">
                        <div class="property-title">' . Html::encode($model->property_name) . '</div>
                        <div class="property-location"><i class="fas fa-location-dot"></i> ' . ($location ? Html::encode($location) : 'Location on request') . '</div>
                        <div class="property-price">' . $price . '</div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-details" data-bs-toggle="modal" data-bs-target="#detailsModal" data-property-id="' . $model->id . '">
                                <i class="fas fa-eye me-1"></i> Details
                            </button>
                            <button type="button" class="btn btn-inquire" data-bs-toggle="modal" data-bs-target="#inquireModal" data-property-id="' . $model->id . '" data-property-name="' . Html::encode($model->property_name) . '">
                                <i class="fas fa-paper-plane me-1"></i> Inquire
                            </button>
                        </div>
                    </div>
                </div>';
        },
        'emptyText' => '<div class="empty-state"><i class="fas fa-house-circle-xmark d-block"></i>No properties are currently available.<br>Please check back soon.</div>',
        'layout' => "{items}\n<div class='mt-4 d-flex justify-content-center'>{pager}</div>",
    ]) ?>
</div>

<!-- Details modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title" id="details-name"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="details-photo" id="details-photo"></div>
            <div class="modal-body p-4">
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <span class="badge rounded-pill text-bg-light border" id="details-type"></span>
                    <span class="text-muted small" id="details-location"><i class="fas fa-location-dot me-1"></i></span>
                </div>
                <div class="fw-bold fs-5 mb-3" style="color:#4f46e5;" id="details-price"></div>
                <p class="text-muted mb-0" id="details-description"></p>
            </div>
            <div class="modal-footer" style="border:none; padding:0 1.5rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-inquire rounded-pill px-4" id="details-inquire-btn" style="margin-top:0;">
                    <i class="fas fa-paper-plane me-1"></i> Inquire Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Inquiry modal -->
<div class="modal fade" id="inquireModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= Url::to(['public-listing/inquire']) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <?= Html::hiddenInput('PropertyInquiry[property_id]', '', ['id' => 'inquire-property-id']) ?>
                <!-- Honeypot: hidden from real users via CSS, bots that autofill forms tend to fill it in -->
                <div style="position:absolute; left:-9999px;">
                    <label>Website</label>
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="modal-header modal-header-gradient">
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
$propertyDetailsJson = json_encode($propertyDetails, JSON_UNESCAPED_SLASHES);
$this->registerJs(<<<JS
var propertyDetails = {$propertyDetailsJson};

// Inquiry modal population
document.getElementById('inquireModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('inquire-property-id').value = button.getAttribute('data-property-id');
    document.getElementById('inquire-property-name').textContent = button.getAttribute('data-property-name');
});

// Details modal population
var currentDetailsId = null;
document.getElementById('detailsModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-property-id');
    var data = propertyDetails[id];
    if (!data) { return; }
    currentDetailsId = id;

    document.getElementById('details-name').textContent = data.name;
    document.getElementById('details-type').textContent = data.type || 'Property';
    document.getElementById('details-location').innerHTML = '<i class="fas fa-location-dot me-1"></i>' + (data.location || 'Location on request');
    document.getElementById('details-price').textContent = data.price ? (data.price + ' / term') : 'Contact for price';
    document.getElementById('details-description').textContent = data.description;

    var photoEl = document.getElementById('details-photo');
    photoEl.innerHTML = data.image
        ? '<img src="' + data.image + '" alt="">'
        : '<i class="fas fa-image"></i>';
});

// Hand off from Details modal straight into the Inquiry modal
document.getElementById('details-inquire-btn').addEventListener('click', function () {
    if (!currentDetailsId) { return; }
    var data = propertyDetails[currentDetailsId];
    var detailsModalEl = document.getElementById('detailsModal');
    var detailsModal = bootstrap.Modal.getInstance(detailsModalEl);

    detailsModalEl.addEventListener('hidden.bs.modal', function openInquire() {
        document.getElementById('inquire-property-id').value = currentDetailsId;
        document.getElementById('inquire-property-name').textContent = data.name;
        new bootstrap.Modal(document.getElementById('inquireModal')).show();
        detailsModalEl.removeEventListener('hidden.bs.modal', openInquire);
    });
    detailsModal.hide();
});

// Staggered scroll-reveal animation for property cards
if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry, i) {
            if (entry.isIntersecting) {
                var el = entry.target;
                var delay = (Array.prototype.indexOf.call(el.parentElement.children, el) % 3) * 0.12;
                el.style.animationDelay = delay + 's';
                el.classList.add('in-view');
                revealObserver.unobserve(el);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal-on-scroll').forEach(function (el) {
        revealObserver.observe(el);
    });
} else {
    document.querySelectorAll('.reveal-on-scroll').forEach(function (el) {
        el.classList.add('in-view');
    });
}
JS);
?>
