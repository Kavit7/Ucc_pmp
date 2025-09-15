<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Property */
/* @var $extraData app\models\PropertyExtraData[] */

$this->title = 'Property Details: ' . $model->property_name;

$css = <<<CSS
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Slab:ital,wght@0,100..700;1,100..700&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
.property-details-container {
    font-family: 'Times roman', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1000px;
    margin: 20px auto;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.urbanist-<uniquifier> {
  font-family: "Urbanist", sans-serif;
  font-optical-sizing: auto;
  font-weight: <weight>;
  font-style: normal;
}

.property-header {
    background: linear-gradient(135deg, #1a8be2ff 0%, #1a8be2ff 100%);
    color: white;
    padding: 20px 25px;
}

.property-header h2 {
    margin: 0;
    font-weight: 600;
    font-size: 24px;
    display: flex;
    align-items: center;
}

.property-header h2 i {
    margin-right: 12px;
    font-size: 28px;
}

.property-content {
    padding: 25px;
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.property-info {
    flex: 1;
    min-width: 300px;
}

.property-image {
    flex: 1;
    min-width: 300px;
    text-align: center;
}

.property-image img {
    width: 100%;
    max-width: 400px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.image-placeholder {
    background: #f3f4f6;
    border-radius: 8px;
    height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 16px;
}

.image-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    color: #9ca3af;
}

.detail-row {
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f1f1;  
}

.detail-label {
    font-weight: 600;
    color: #4b5563;
    font-size: 15px;
    margin-bottom: 5px;
}

.detail-value {
    color: #1f2937;
    font-size: 15px;
    margin:auto;
}

.status-badge {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.status-active { background-color: #10b981; color: white; }
.status-rented { background-color: #f59e0b; color: white; }
.status-unknown { background-color: #9ca3af; color: white; }

.description-box {
    background: #f9fafb;
    border-left: 4px solid #3d65e9ff;
    padding: 12px;
    border-radius: 0 6px 6px 0;
}

.extra-attributes {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.extra-header {
    font-size: 18px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.extra-header i {
    margin-right: 10px;
    color: #1a8be2ff;
}

.back-button {
    display: inline-flex;
    align-items: center;
    margin-top: 25px;
    padding: 10px 20px;
    background: #1a8be2ff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: background 0.3s;
}

.back-button:hover {
    background: #1a8be2ff;
    color: white;
}

.back-button i {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .property-content {
        flex-direction: column;
    }
}
CSS;
$this->registerCss($css);
?>

<div class="property-details-container">
    <div class="property-header">
        <h2><i class="fas fa-home"></i> Property Details</h2>
    </div>
    
    <div class="property-content">
        <div class="property-info">
            <div class="detail-row ">
                <div class="detail-label">Property Type</div>
                 <span class="detail-value "><?= $model->propertyType->list_Name ?? '-' ?></span>
                
            </div>
            
            <div class="detail-row ">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="status-badge status-<?= strtolower($model->propertyStatus->list_Name ?? 'unknown') ?>">
                        <?= $model->propertyStatus->list_Name ?? '-' ?>
                    </span>
</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Identifier Code</div>
                <div class="detail-value"><?= Html::encode($model->identifier_code) ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Ownership Type</div>
                <div class="detail-value"><?= $model->propertyOwnerShip->list_Name ?? '-' ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Description</div>
                <div class="detail-value">
                    <div class="description-box">
                        <?= Html::encode($model->description) ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($extraData)): ?>
                <div class="extra-attributes">
                    <div class="extra-header">
                        <i class="fas fa-list-alt"></i> Extra Attributes
                    </div>
                    
                    <?php foreach ($extraData as $extra): ?>
                        <div class="detail-row">
                            <div class="detail-label"><?= Html::encode($extra->propertyAttribute->attribute_name ?? '-') ?></div>
                            <div class="detail-value">
                                <?php 
                                if ($extra->attribute_answer_text) {
                                    echo Html::encode($extra->attribute_answer_text);
                                } elseif ($extra->attribute_answer_id) {
                                    echo Html::encode($extra->attributeAnswer->listSource->list_Name ?? '-');
                                } else {
                                    echo '-';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="property-image">
            <?php if ($model->document_url): ?>
                <?= Html::img('@web/' . $model->document_url, ['alt' => 'Property Image']) ?>
            <?php else: ?>
                <div class="image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>No Image Available</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="padding: 0 25px 25px;">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to List', ['index'], ['class' => 'back-button']) ?>
    </div>
</div>
