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
    .add-user-btn { position: absolute; top: 20px; right: 20px; background: #007bff; color: #fff; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 8px; }
    .filters { text-align: center; margin-top: 80px; font-size: 16px; font-weight: bold; }
    .filters a { margin: 0 12px; color: black; text-decoration: none; font-weight: bold; }
    .filters a.active { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 3px; }
    .user-table { margin-top: 30px; background: #fff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); overflow-x: auto; }
    .user-table table { width: 100%; border-collapse: collapse; }
    .user-table th, .user-table td { padding: 10px; border-bottom: 1px solid #ddd; color: black; }
    .user-table th { background: #f1f1f1; text-align: left; }
    .btn-action { padding: 4px 12px; border-radius: 5px; border: none; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-edit { background-color: #007bff; color: #fff; }
    .btn-delete { background-color: #dc3545; color: #fff; }
    .no-users { text-align: center; padding: 40px; font-size: 18px; color: #777; }
");

$currentFilter = $filter ?? 'all';
$currentRole = Yii::$app->user->identity->role ?? null;
?>

<div class="content user-container">
    <?= Html::a('<span style="font-size:18px;">＋</span> Add User', ['create'], ['class' => 'add-user-btn']) ?>

    <!-- Navigation Menu -->
    <div class="filters">
        <?= Html::a('All Users', ['index', 'filter' => 'all'], ['class' => $currentFilter == 'all' ? 'active' : '', 'style'=>($currentRole=='manager'?'display:none;':'')]) ?>
        <?= Html::a('Admins', ['index', 'filter' => 'admin'], ['class' => $currentFilter == 'admin' ? 'active' : '', 'style'=>($currentRole=='manager'?'display:none;':'')]) ?>
        <?= Html::a('Managers', ['index', 'filter' => 'manager'], ['class' => $currentFilter == 'manager' ? 'active' : '', 'style'=>($currentRole=='manager'?'display:none;':'')]) ?>
        <?= Html::a('Tenants', ['index', 'filter' => 'tenant'], ['class' => $currentFilter == 'tenant' ? 'active' : '']) ?>
    </div>

    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>Users</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Phone</th>
                    <th>Properties</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $models = $dataProvider->getModels();
                if ($currentRole === 'manager') {
                    $models = array_filter($models, fn($m) => $m->role === 'tenant');
                }

                if (count($models) === 0): ?>
                    <tr>
                        <td colspan="6" class="no-users">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($models as $model): ?>
                        <tr>
                            <td class="d-flex flex-column">
                                <div><?= Html::encode($model->full_name) ?></div>
                                <span><?= Html::encode($model->email) ?></span>
                            </td>
                            <td><?= Html::encode($model->role) ?></td>
                            <td>
                                <?php 
                                switch ($model->status) {
                                    case 'blocked': $bg='#FFBEBF'; $color='#F61616'; break;
                                    case 'inactive': $bg='#D3D5D3'; $color='#6E6E6E'; break;
                                    case 'active': $bg='#D9FFDC'; $color='#28A745'; break;
                                    default: $bg='#D9FFDC'; $color='#28A745';
                                }
                                ?>
                                <span style="background: <?= $bg ?>; color: <?= $color ?>; padding:5px 10px; border-radius:15px;">
                                    <?= Html::encode($model->status) ?>
                                </span>
                            </td>
                            <td><?= Html::encode($model->phone) ?></td>
                            <td>None</td>
                            <td>
                                <?= Html::a('Edit', ['update', 'id'=>$model->user_id], ['class'=>'text-decoration-none me-3']) ?>
                                
                                <?php if($currentRole === 'admin'): ?>
                                    <?= Html::a('Delete', ['delete', 'id'=>$model->user_id], [
                                        'class'=>'text-danger text-decoration-none',
                                        'data-method'=>'post',
                                        'data-confirm'=>'Do you want to delete?'
                                    ]) ?>
                                <?php elseif($currentRole === 'manager'): ?>
                                    <?= Html::a('Delete', '#', [
                                        'class'=>'text-danger text-decoration-none',
                                        'onclick'=>"Swal.fire('Access Denied','You cannot delete tenants. Contact admin.','error'); return false;"
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['depends'=>[\yii\web\JqueryAsset::class]]);
?>
