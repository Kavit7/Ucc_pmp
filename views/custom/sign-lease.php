<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Lease $lease */

$this->title = 'Sign Lease';
?>

<div class="container mt-5" style="max-width: 620px;">
    <h1 class="mb-2 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>
    <p class="text-muted mb-4"><?= Html::encode($lease->property->property_name ?? 'Property') ?> &middot; <?= Html::encode($lease->lease_start_date) ?> to <?= Html::encode($lease->lease_end_date) ?></p>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <p>By signing below, you confirm you have read and agree to the terms of this lease.</p>

            <label class="form-label fw-semibold mb-2">Draw your signature</label>
            <canvas id="sigPad" width="560" height="180" class="signature-pad"></canvas>
            <div class="d-flex justify-content-between mt-2 mb-4">
                <button type="button" id="clearSig" class="btn btn-sm btn-outline-secondary">Clear</button>
                <span class="text-muted small align-self-center">Use your mouse or finger</span>
            </div>

            <?= Html::beginForm(['custom/sign-lease', 'id' => $lease->id], 'post', ['id' => 'signForm']) ?>
                <?= Html::hiddenInput('signature_data', '', ['id' => 'signatureData']) ?>
                <div class="d-flex gap-2">
                    <button type="submit" id="submitSig" class="btn btn-primary" disabled>Sign Lease</button>
                    <?= Html::a('Cancel', ['view-lease', 'tenant' => $lease->tenant_id], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<style>
    .signature-pad {
        width: 100%;
        max-width: 560px;
        height: 180px;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        background: #fff;
        touch-action: none;
        cursor: crosshair;
    }
</style>

<?php
$this->registerJs(<<<JS
(function () {
    var canvas = document.getElementById('sigPad');
    var ctx = canvas.getContext('2d');
    ctx.lineWidth = 2.2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#111827';

    var drawing = false;
    var hasDrawn = false;
    var submitBtn = document.getElementById('submitSig');

    function pos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var point = e.touches ? e.touches[0] : e;
        return {
            x: (point.clientX - rect.left) * scaleX,
            y: (point.clientY - rect.top) * scaleY,
        };
    }

    function start(e) {
        drawing = true;
        var p = pos(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        e.preventDefault();
    }

    function move(e) {
        if (!drawing) { return; }
        var p = pos(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        hasDrawn = true;
        submitBtn.disabled = false;
        e.preventDefault();
    }

    function end() { drawing = false; }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);

    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    canvas.addEventListener('touchend', end);

    document.getElementById('clearSig').addEventListener('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDrawn = false;
        submitBtn.disabled = true;
    });

    document.getElementById('signForm').addEventListener('submit', function () {
        document.getElementById('signatureData').value = hasDrawn ? canvas.toDataURL('image/png') : '';
    });
})();
JS);
?>
