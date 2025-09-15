<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\ListSource;
use app\models\PropertyAttribute;
use app\models\PropertyExtraData;
use app\models\PropertyAttributeAnswer;
use app\models\Street;

/* @var $this yii\web\View */
/* @var $model app\models\Property */
/* @var $form yii\widgets\ActiveForm */
/* @var $childUsage array */
/* @var $childProperty array */
/* @var $childOwner array */
/* @var $childStatus array */

$this->title = $model->property_name;
$this->params['breadcrumbs'][] = ['label' => 'Properties', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->property_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

// Fetch extra data for this property
$extraData = PropertyExtraData::find()->where(['property_id' => $model->id])->all();
$extraMap = [];
foreach ($extraData as $extra) {
    $extraMap[$extra->property_attribute_id] = [
        'answer_id' => $extra->attribute_answer_id,
        'text'      => $extra->attribute_answer_text,
    ];
}

// Fetch attributes for this property type
$propertyAttributes = PropertyAttribute::find()->where(['property_type_id' => $model->property_type_id])->all();

// Custom CSS
$css = <<<CSS
.property-update {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #374151;
}

.property-update h3 {
    color: #4f46e5;
    font-weight: 700;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.75rem;
}

.card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section-title {
    color: #4f46e5;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    color: #374151;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0.75rem 1rem;
    color: #374151;
    transition: border-color 0.15s ease;
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    outline: none;
}

.current-document {
    background-color: #f9fafb;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin-top: 0.5rem;
    border: 1px solid #e5e7eb;
}

.current-document label {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    display: block;
}

.current-document a {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 500;
}

.current-document a:hover {
    text-decoration: underline;
}

.btn-primary {
    background-color: #4f46e5;
    border-color: #4f46e5;
    color: white;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: background-color 0.15s ease;
}

.btn-primary:hover {
    background-color: #4338ca;
    border-color: #4338ca;
}

.btn-secondary {
    background-color: #6b7280;
    border-color: #6b7280;
    color: white;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: background-color 0.15s ease;
}

.btn-secondary:hover {
    background-color: #4b5563;
    border-color: #4b5563;
}

.border-top {
    border-top: 1px solid #e5e7eb !important;
}

.pt-3 {
    padding-top: 1rem !important;
}

.ml-2 {
    margin-left: 0.5rem !important;
}

/* Style for extra attributes section */
.extra-attribute-label {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .ml-2 {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
CSS;

$this->registerCss($css);
?>

<div class="property-update container mt-4">
    <h3 class="mb-4"><?= Html::encode($this->title) ?></h3>

    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <!-- Basic Information -->
            <div class="form-section">
                <h5 class="form-section-title">Basic Information</h5>
                
                <?= $form->field($model, 'property_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'usage_type_id')->dropDownList($childUsage, ['prompt' => 'Select Usage Type']) ?>
                <?= $form->field($model, 'property_type_id')->dropDownList($childProperty, ['prompt' => 'Select Property Type']) ?>
                <?= $form->field($model, 'ownership_type_id')->dropDownList($childOwner, ['prompt' => 'Select Ownership Type']) ?>
                <?= $form->field($model, 'property_status_id')->dropDownList($childStatus, ['prompt' => 'Select Status']) ?>
                <?= $form->field($model, 'street_id')->dropDownList(ArrayHelper::map(Street::find()->all(),'street_id','street_name'), ['prompt' => 'Select Street']) ?>
            </div>
            
            <!-- Document -->
            <div class="form-section">
                <h5 class="form-section-title">Document</h5>
                <?= $form->field($model, 'documentFile')->fileInput() ?>
                <?php if ($model->document_url): ?>
                    <div class="current-document">
                        <label>Current Document:</label>
                        <?= Html::a(basename($model->document_url), $model->document_url, ['target' => '_blank']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Extra Attributes -->
            <div class="form-section">
                <h5 class="form-section-title">Extra Attributes</h5>
                <?php foreach ($propertyAttributes as $attr): ?>
                    <div class="form-group">
                        <label class="extra-attribute-label"><?= Html::encode($attr->attribute_name) ?></label>
                        <?php
                        $saved = $extraMap[$attr->id] ?? null;
                        $value = '';
                        $options = [];

                        // Determine input type
                        $dataType = $attr->dataType->list_Name ?? 'text';

                        if ($dataType === 'text' || $dataType === 'number') {
                            $value = $saved['text'] ?? '';
                            echo Html::input($dataType, "attributes[{$attr->id}]", $value, ['class' => 'form-control']);
                        } elseif ($dataType === 'boolean' || $dataType === 'select') {
                            $selected = null;

                            if ($saved && $saved['answer_id']) {
                                $answerRecord = PropertyAttributeAnswer::findOne($saved['answer_id']);
                                if ($answerRecord) {
                                    $selected = $answerRecord->id;
                                    $listItem = ListSource::findOne($answerRecord->answer_id);
                                    if ($listItem) {
                                        $siblings = ListSource::find()->where(['parent_id' => $listItem->parent_id])->all();
                                        $listOptions = ArrayHelper::map($siblings, 'id', 'list_Name');

                                        // remap ListSource.id → PropertyAttributeAnswer.id
                                        $attrAnswers = PropertyAttributeAnswer::find()
                                            ->where(['property_attribute_id' => $attr->id])
                                            ->andWhere(['answer_id' => array_keys($listOptions)])
                                            ->all();

                                        $mapping = [];
                                        foreach ($attrAnswers as $aa) {
                                            $mapping[$aa->answer_id] = $aa->id;
                                        }

                                        foreach ($listOptions as $lsId => $lsName) {
                                            if (isset($mapping[$lsId])) {
                                                $options[$mapping[$lsId]] = $lsName;
                                            }
                                        }
                                    }
                                }
                            }

                            echo Html::dropDownList("answers[{$attr->id}]", $selected, $options, [
                                'class' => 'form-control',
                                'prompt' => 'Select Option'
                            ]);
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Actions -->
            <div class="form-group pt-3 border-top">
                <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary ml-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>