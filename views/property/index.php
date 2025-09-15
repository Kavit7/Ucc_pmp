<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\PropertyPrice;
?>

<div class="container mt-4 custom-container">

    <!-- ✅ Add New Property Button Above Cards -->
    <div class="d-flex justify-content-end mb-4">
        <a href="<?= Url::to(['property/create']) ?>" class="btn btn-add-property">
            Add New Property
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="row">
        <?php foreach($models as $model): ?>
        <div class="col-lg-4 col-md-4 col-sm-6 mb-4 ">
            <div class="card property-card-sm shadow-sm border-0 rounded-3">

                <!-- Property Image -->
                <div class="property-img-wrapper">
                    <?php if ($model->document_url): ?>
                    <img src="<?= Yii::getAlias('@web/' . $model->document_url) ?>" alt="Property Image"
                        class="card-img-top property-img-sm">
                    <?php else: ?>
                    <div class="no-image-sm d-flex align-items-center justify-content-center">
                        No Image
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Card Body -->
                <div class="card-body text-center p-3">
                    <!-- Property Name -->
                    <h6 class="fw-bold mb-1 property-title">
                        <?= Html::encode($model->property_name) ?>
                    </h6>
                    <!-- Location -->
                    <p class="property-location small mb-2">
                        <?= Html::encode($model->street->street_name ?? 'No Location') ?>
                    </p>

                    <!-- Divider -->
                    <hr class="my-2 divider">

                    <!-- Status -->
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="label-text">Status</span>
                        <span class="status-available"><?= $model->propertyStatus->list_Name?></span>
                    </div>

                    <!-- Price -->
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="label-text">Price</span>
                        <?php
                       $priceModel = PropertyPrice::find()->where(['property_id' => $model->id])->one();
                       $priceValue = $priceModel ? $priceModel->unit_amount : 0;
                     ?>
                        <span class="price-text"><?= number_format($priceValue) ?> Tsh</span>

                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= Url::to(['property/document', 'id'=>$model->id]) ?>"
                            class="btn btn-outline-dark btn-sm w-50 me-1">
                            View details
                        </a>
                        <a href="<?= Url::to(['property/update', 'id'=>$model->id]) ?>"
                            class="btn btn-outline-dark btn-sm w-50 ms-1">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Styles -->
<?php $this->registerCss("
.custom-container {
    background-color: whitesmoke;
    padding: 25px;
    border-radius: 12px;
}

/* Card */
.property-card-sm {
    width: 360px;
    border-radius: 15px;
    background-color: #ffffff;
    overflow: hidden;
}

/* Image Wrapper */
.property-img-wrapper {
    height: 180px;
    border-radius: 15px;
    overflow: hidden;
}

/* Property Image */
.property-img-sm {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.1);
    /* zoom in effect */
}

/* No Image Placeholder */
.no-image-sm {
    height: 180px;
    background-color: #e5e7eb;
    color: #9ca3af;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 15px;
}

.property-title {
    color: #111827;
}

.property-location {
    color: #6b7280;
}

.divider {
    border-color: #d1d5db;
    opacity: 1;
}

.label-text {
    color: #374151;
}

.status-available {
    color: #16a34a;
    font-weight: 600;
}

.price-text {
    color: #111827;
    font-weight: 600;
}

.btn-sm {
    font-size: 0.8rem;
    border-radius: 8px;
    padding: 6px 0;
}

/* Add New Property Button */
.btn-add-property {
    background-color: #007bff;
    /* blue background */
    color: #ffffff;
    border-radius: 8px;
    padding: 6px 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-add-property:hover {
    background-color: #0056b3;
    color: #ffffff;
}"
);

