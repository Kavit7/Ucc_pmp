<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\ListSource;

$this->title = 'Configure List Source';
$form = ActiveForm::begin();
$this->registerJs("
$(document).ready(function () {
    $('.form-select').on('change', function() {
        let select = $(this).val();
        if (select === 'New-List') {
            $('.List').fadeIn();
            $('.ChildList').hide();
        } else if (select === 'Existing-List') {
            $('.List').hide();
            window.location.href='list-source/add-child'
        } else {
            $('.List, .ChildList').hide();
        }
    });
});
");
?>

<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3f51b5;
            --primary-dark: #0c0a7e;
            --secondary: #f8f9fa;
            --accent: #ff6b6b;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 10px 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            border: none;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .form-select, .form-control {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(63, 81, 181, 0.25);
        }
        
        .config-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary);
        }
        
        .config-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .config-title i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .select-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>
        <div class="container mb-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="bi bi-sliders me-2"></i>
                    <span>Configuration Options</span>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="select-card">
                    <label class="form-label">Select Configuration Task</label>
                    <select class="form-select form-select-lg">
                        <option value="">------- Select Configuration Task ---------</option>
                        <option value="New-List">➕ Add New List Name Configuration</option>
                        <option value="Existing-List">🔗 Add Configuration To Existing List Name</option>
                    </select>
                </div>

                <!-- New List Form -->
                <div class="List" style="display:none;">
                    <div class="config-card">
                        <h5 class="config-title"><i class="bi bi-plus-circle"></i> Add New List Name</h5>
                        <?= $this->render('_formview', ['model' => $model]) ?>
                    </div>
                </div>

                <!-- Child List Form -->
            </div>
        </div>

        <!-- Source Table -->
        <div class="mt-5">
            <?= $this->render('source', ['sources' => $sources]) ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php ActiveForm::end(); ?>