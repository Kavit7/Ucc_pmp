<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\PropertyPrice;
use yii\widgets\ListView;
use yii\helpers\ArrayHelper;
?>

<div class="container mt-4 custom-container">

    <div class="d-flex justify-content-end mb-4">
        <a href="<?= Url::to(['property/create']) ?>" class="btn btn-add-property">
            Add New Property
        </a>
    </div>
      <div class="property-search mb-4 bg-white p-3 rounded ">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' => ['class' => 'row g-2'],
    ]); ?>

    <div class="col-md-3">
        <?= $form->field($searchModel, 'property_name')->textInput(['placeholder' => 'Search by name','class'=>'styled-input'])->label(false) ?>
    </div>

    <div class="col-md-3">
      <?= $form->field($searchModel, 'ownership_type_id')->dropDownList(
    $childOwner,
    [
        'prompt' => 'Search Ownership Types',
        'class' => 'form-select styled-select'
    ]
)->label(false) ?>

    </div>

    <div class="col-md-3">
        <?= $form->field($searchModel, 'property_status_id')->dropDownList($childStatus,[
            'prompt' => 'Search By Status',
            'class'=>'form-select styled-select',
        ], )->label(false) ?>
    </div>

    <div class="col-md-3 d-flex justify-content-between">
        <?= Html::submitButton('Filter', ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary mt-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div> 
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'row'], // parent row
        'itemOptions' => ['class' => 'col-lg-4 col-md-6 col-sm-12 mb-4'],
         // each card column
        'itemView' => function ($model) {
            $priceModel = PropertyPrice::find()->where(['property_id' => $model->id])->one();
            $priceValue = $priceModel ? $priceModel->unit_amount : 0;
             
            $image = $model->document_url
                ? '<img src="' . Yii::getAlias('@web/' . $model->document_url) . '" alt="Property Image" class="card-img-top property-img-sm">'
                : '<div class="no-image-sm d-flex align-items-center justify-content-center">No Image</div>';

            return '
                <div class="card property-card-sm shadow-sm border-0 rounded-3">
                    <div class="property-img-wrapper">' . $image . '</div>
                    <div class="card-body text-center p-3">
                        <h6 class="fw-bold mb-1 property-title">' . Html::encode($model->property_name) . '</h6>
                        <p class="property-location small mb-2">' . Html::encode($model->street->street_name ?? 'No Location') . '</p>
                        <hr class="my-2 divider">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="label-text">Status</span>
                            <span class="status-available">' . Html::encode($model->propertyStatus->list_Name) . '</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="label-text">Price</span>
                            <span class="price-text">' . number_format($priceValue) . ' Tsh</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="' . Url::to(['property/document', 'id'=>$model->id]) . '" class="btn btn-outline-dark btn-sm w-50 me-1">View details</a>
                            <a href="' . Url::to(['property/update', 'id'=>$model->id]) . '" class="btn btn-outline-dark btn-sm w-50 ms-1">Edit</a>
                        </div>
                    </div>
                </div>';
        },
        'layout' => "{items}\n<div class='col-12 mt-3'>{pager}</div>",
        'pager'=>[
            'class'=>\yii\widgets\LinkPager::class,
            'options'=>['class'=>'pagination justify-content-center'],
            'linkOptions'=>['class'=>'page-link'],
            'prevPageLabel'=>'&laquo',
            'nextPageCssClass'=>'active',
            'disabledPageCssClass'=>'disabled',
        ],
    ]) ?>
</div>

<?php $this->registerCss(" .custom-container { background-color: whitesmoke; padding: 25px; border-radius: 12px; } /* Card */ .property-card-sm { width: 360px; border-radius: 15px; background-color: #ffffff; overflow: hidden; } /* Image Wrapper */ .property-img-wrapper { height: 180px; border-radius: 15px; overflow: hidden; } /* Property Image */ .property-img-sm { width: 100%; height: 100%; object-fit: cover; transform: scale(1.1); /* zoom in effect */ } /* No Image Placeholder */ .no-image-sm { height: 180px; background-color: #e5e7eb; color: #9ca3af; font-weight: 600; font-size: 1rem; border-radius: 15px; } .property-title { color: #111827; } .property-location { color: #6b7280; } .divider { border-color: #d1d5db; opacity: 1; } .label-text { color: #374151; } .status-available { color: #16a34a; font-weight: 600; } .price-text { color: #111827; font-weight: 600; } .btn-sm { font-size: 0.8rem; border-radius: 8px; padding: 6px 0; } /* Add New Property Button */ .btn-add-property { background-color: #007bff; /* blue background */ color: #ffffff; border-radius: 8px; padding: 6px 12px; font-weight: 600; text-decoration: none; transition: background-color 0.3s; } .btn-add-property:hover { background-color: #0056b3; color: #ffffff; }
.pagination{
    gap:0.3rem
    }
.pagination .page-link{
   background-color:#007bff;
   color:white;
   border-radius:8px;
   padding:6px 12px;
   transition:all 0.3s;
    }
   .pagination .page-link:hover{
    background-color:#0056b3;
    color:white;
    }
    .pagination .active .page-link{
    background-color:#16a34a;
    border-color:#16a34a;
    color:white;
    }
    .pagination .disabled .page-link{
   background-color:#d1d5db;
   color:#9ca3af;
   pointer-events:none;

    }
  .styled-input, .styled-select, .styled-textarea {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--dark-text);
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: var(--light-bg);
    }
    
    .styled-input:focus, .styled-select:focus, .styled-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        background-color: #ffffff;
    }
    
    .styled-textarea {
        min-height: 120px;
        resize: vertical;
    }
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
" );