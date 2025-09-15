<?php
use yii\helpers\Html;


?>
<div class="mb-3 title font-weight-bold justify-content-between d-flex">
         <a  href=<?=\yii\helpers\Url::to(['property/index'])?> class="btn btn-view ">All Property</a>

       <a  href=<?=\yii\helpers\Url::to(['property/sales'])?> class="btn btn-view ">Sales Property</a>
        <a  href=<?=\yii\helpers\Url::to(['property/rented'])?> class="btn btn-view ">Rented Property</a>
         <a  href=<?=\yii\helpers\Url::to(['property/stores'])?> class="btn btn-view ">Storage Property</a>

</div>
<div>

<div class="row">
<?php foreach($Rents as $Rent):?>
      <?php if($Rent->usageType->list_Name ==='Rented'):?>
 <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm h-100 border-0 rounded property-card-sm">
                    <!-- Image -->
                    <?php if ($Rent->document_url): ?>
                        <img src="<?= Yii::getAlias('@web/' . $Rent->document_url) ?>" 
                             alt="Property Image" 
                             class="card-img-top property-img-sm">
                    <?php else: ?>
                        <div class="no-image-sm d-flex align-items-center justify-content-center">
                            No Image
                        </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column p-3">
                        <!-- Property Name -->
                        <h6 class="card-title font-weight-bold text-truncate"><?= Html::encode($Rent->property_name) ?></h6>

                        <!-- Usage Type -->
                        <p class="mb-3 small">
                            Usage Type: <?= Html::encode($Rent->usageType ? $Rent->usageType->list_Name : 'N/A') ?>
                        </p>
                        
                        <!-- Ownership -->
                        <p class="mb-3 small">
                            Ownership: <?= Html::encode($Rent->propertyOwnerShip ? $Rent->propertyOwnerShip->list_Name : 'N/A') ?>
                        </p>

                        <!-- Buttons -->
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="<?= \yii\helpers\Url::to(['property/document', 'id'=>$Rent->id]) ?>" class="btn btn-view btn-sm shadow-sm d-flex align-items-center ">
                                <i class="fas fa-eye icon-sm mr-1"></i> View
                            </a>
                            <a href="<?= \yii\helpers\Url::to(['property/update', 'id'=>$Rent->id]) ?>" class="btn btn-edit btn-sm shadow-sm d-flex align-items-center">
                                <i class="fas fa-edit icon-sm mr-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    <?php endif ?>
<?php endforeach ?>
</div>
</div>
<style>
    body {
    font-family: 'Inter', 'Roboto', sans-serif;
    font-size: 15px;
    color: #1f2937;
    background-color: #f9fafb;
    line-height: 1.6;
}
.title{
    color: #780ab8ff;
}
.property-card-sm {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
    font-size: 0.9rem;
}
.property-card-sm:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.12);
}
.property-img-sm {
    height: 140px;
    object-fit: cover;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}
.no-image-sm {
    height: 140px;
    background-color: #f8f9fa;
    color: #999;
    font-weight: 600;
    font-size: 1rem;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* New small button styles */
.btn-view, .btn-edit {
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 4px 12px;
    transition: background 0.3s ease;
    min-width: 70px;
    justify-content: center;
}
.btn-view {
    background: linear-gradient(45deg, #634dceff, #853fe7ff);
    color: white;
    border: none;
}
.btn-view:hover {
    background: linear-gradient(45deg, #4e0b9fff, #4e9af1);
    color: white;
    text-decoration: none;
}
.btn-edit {
    background: linear-gradient(45deg, #f1a94e, #a67a08ff);
    color: white;
    border: none;
}
.btn-edit:hover {
    background: linear-gradient(45deg, #f2b71c, #f1a94e);
    color: white;
    text-decoration: none;
}

/* Icon size and spacing */
.icon-sm {
    font-size: 0.75rem;
}
.mr-1 {
    margin-right: 0.25rem !important;
}
</style>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Inter:wght@400;500;600;700&display=swap";


