<?php 
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $filter */

$this->title = 'User Management';
$this->registerCssFile('https://fonts.cdnfonts.com/css/sf-pro-display');
$this->registerCss("
    body { font-family: 'SF Pro Display', sans-serif; background: #f4f4f4; }
    .user-container { position: relative; padding: 20px; }
    .add-user-btn {
        position: absolute;
        top: 20px; right: 20px;
        background: #007bff; color: #fff;
        padding: 10px 18px; border-radius: 8px;
        text-decoration: none; font-weight: 500;
        display: flex; align-items: center; gap: 8px;
    }
    .filters {
        text-align: center;
        margin-top: 80px;
        font-size: 16px; font-weight: bold;
    }
    .filters a {
        margin: 0 12px;
        color: black;
        text-decoration: none;
        font-weight: bold;
    }
    .filters a.active {
        color: #007bff;
        border-bottom: 2px solid #007bff;
        padding-bottom: 3px;
    }
    .user-table {
        margin-top: 30px;
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        overflow-x: auto;
    }
    .user-table table {
        width: 100%;
        border-collapse: collapse;
    }
    .user-table th, .user-table td {
        padding: 10px;
        border: 1px solid #ddd;
        color: black; /* force black headings and text */
    }
    .user-table th {
        background: #f1f1f1;
        text-align: left;
    }
    .btn-action {
        padding: 4px 12px;
        border-radius: 5px;
        border: none;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-edit { background-color: #007bff; color: #fff; }
    .btn-delete { background-color: #dc3545; color: #fff; }
    .no-users {
        text-align: center;
        padding: 40px;
        font-size: 18px;
        color: #777;
    }
");

$currentFilter = $filter ?? 'all';
?>

<div class="content user-container">
    <!-- Add User Button -->
    <?= Html::a('<span style="font-size:18px;">＋</span> Add User', ['create'], ['class' => 'add-user-btn']) ?>

    <!-- Filters -->
    <div class="filters">
        <?= Html::a('All Users', ['index', 'filter' => 'all'], ['class' => $currentFilter == 'all' ? 'active' : '']) ?>
        <?= Html::a('Admins', ['index', 'filter' => 'admin'], ['class' => $currentFilter == 'admin' ? 'active' : '']) ?>
        <?= Html::a('Managers', ['index', 'filter' => 'manager'], ['class' => $currentFilter == 'manager' ? 'active' : '']) ?>
        <?= Html::a('Tenants', ['index', 'filter' => 'tenant'], ['class' => $currentFilter == 'tenant' ? 'active' : '']) ?>
    </div>

    <!-- Users Table -->
    <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>Users</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Phone</th>
                        <th>Properties</th><!-- new column -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataProvider->getTotalCount() == 0): ?>
                        <tr>
                            <td colspan="6" class="no-users">No users found.</td>
                        </tr>
                    <?php else: ?>
                    <?php foreach ($dataProvider->getModels() as $model): ?>
                        <tr>
                            <td><?= Html::encode($model->full_name) ?></td>
                            <td><?= Html::encode($model->role) ?></td>
                            <td>
                                <?php 
                                    switch ($model->status) {
                                        case 'Blocked':
                                            $bgColor = '#FFBEBF'; 
                                            $textColor = '#F61616'; 
                                            break;
                                        case 'Inactive':
                                            $bgColor = '#b3b3b4ff'; 
                                            $textColor = '#5b5b5cff'; 
                                        case 'Active':
                                        default:
                                            $bgColor = '#D9FFDC'; 
                                            $textColor = '#28A745';
                                    }
                                ?>
                                <span style="background: <?= $bgColor ?>; color: <?= $textColor ?>; padding: 5px 10px; border-radius: 15px;">
                                    <?= Html::encode($model->status) ?>
                                </span>

                            </td>
                            <td><?= Html::encode($model->phone) ?></td>
                            <td>—</td> <!-- placeholder for properties count -->
                            <td>
                                <?= Html::a('Edit', ['update', 'id' => $model->user_id], ['class' => 'btn-action btn-edit']) ?>
                                <?= Html::a('Delete', ['delete', 'id' => $model->user_id], [
                                    'class' => 'btn-action btn-delete',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Do you want to delete?'
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
