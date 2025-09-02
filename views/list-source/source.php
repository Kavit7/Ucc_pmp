<?php
use yii\helpers\Html;
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
        
        .table th {
            background-color: #f7f9fc;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-top: 1px solid var(--border);
            border-bottom: 2px solid var(--border);
            padding: 1rem 0.75rem;
        }
        
        .table td {
            padding: 1.2rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
        }
        
        .table tr {
            transition: all 0.2s ease;
        }
        
        .table tr:hover {
            background-color: #f7f9fc;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .action-btn {
            transition: all 0.2s ease;
            padding: 0.5rem;
            border-radius: 6px;
        }
        
        .action-btn:hover {
            background-color: #f0f4ff;
            transform: scale(1.1);
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .text-muted {
            color: var(--text-light) !important;
        }
        
        @media (max-width: 992px) {
            .table-responsive {
                border-radius: 8px;
                border: 1px solid var(--border);
            }
        }
    </style>
</head>
<body>
    <div class="container mb-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="bi bi-table me-2"></i>
                    <span>LIST SOURCE DETAILS</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>UUID</th>
                                <th>LIST NAME</th>
                                <th>CODE</th>
                                <th>CATEGORY</th>
                                <th>PARENT ID</th>
                                <th>DESCRIPTION</th>
                                <th>CREATED</th>
                                <th>UPDATED</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sources as $source): ?>
                            <tr>
                                <td class="fw-bold text-primary">#<?= $source->id ?></td>
                                <td><?= $source->uuid ?></td>
                                <td class="text-capitalize fw-medium"><?= $source->list_Name ?></td>
                                <td><span class="badge bg-primary"><?= $source->code ?></span></td>
                                <td><?= $source->category ?></td>
                                <td><?= $source->parent_id ?></td>
                                <td class="text-muted"><?= $source->description ?></td>
                                <td><span class="badge bg-success"><?= $source->created_at ?></span></td>
                                <td><span class="badge bg-warning text-dark"><?= $source->updated_at ?></span></td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <?= Html::a(
                                            '<i class="bi bi-pencil-square"></i>',
                                            ['list-source/update', 'id' => $source->id],
                                            ['class' => 'action-btn text-primary me-2']
                                        ) ?>
                                        <?= Html::a(
                                            '<i class="bi bi-trash3"></i>',
                                            ['list-source/delete','id'=>$source->id],
                                            [
                                                'class' => 'action-btn text-danger',
                                                'data' => [
                                                    'confirm' => 'Are you sure You want to delete this Item?',
                                                    'method' => 'post',
                                                ],
                                            ]
                                        ) ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>