<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Users;
use yii\data\ActiveDataProvider;

class UsersController extends Controller
{
    public $layout = 'custom';
    public function behaviors()
{
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::class,
            'only' => ['index', 'view', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                return Yii::$app->response->redirect(['login/login']);
            },
        ],
    ];
}


public function actionCreate()
{
    $model = new Users();

    if ($model->load(Yii::$app->request->post())) {
        // Debug raw POST
        Yii::debug(Yii::$app->request->post(), __METHOD__);

        // --- Grab privileges ---
        $model->privileges = Yii::$app->request->post('Users')['privileges'] ?? [];

        // --- UUID generation ---
        if (empty($model->uuid)) {
            $lastNumber = (int) Users::find()
                ->select(['MAX(CAST(SUBSTRING(uuid,6) AS UNSIGNED)) AS maxNumber'])
                ->where(['like', 'uuid', 'User_%', false])
                ->scalar();

            $lastNumber = $lastNumber ?: 0;
            $model->uuid = 'User_' . ($lastNumber + 1);
        }

        // --- Password handling ---
        $postData = Yii::$app->request->post('Users');
        if (!empty($postData['password'])) {
    $model->password = Yii::$app->security->generatePasswordHash($postData['password']);

        } 

        // --- Save user ---
        if ($model->save()) {
            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->user_id);

            $role = $auth->getRole($model->role);
            if ($role) {
                $auth->assign($role, $model->user_id);
            }

            if (!empty($model->privileges) && is_array($model->privileges)) {
                foreach ($model->privileges as $permName) {
                    $permission = $auth->getPermission($permName);
                    if ($permission) {
                        $auth->assign($permission, $model->user_id);
                    }
                }
            }

            Yii::$app->session->setFlash('success', 'User created successfully!');
            return $this->redirect(['index']);
        } else {
            // 🔴 Show validation + DB errors
            Yii::error($model->errors, __METHOD__);
            Yii::$app->session->setFlash('error', json_encode($model->errors, JSON_PRETTY_PRINT));
        }
    } else {
        // 🔴 If data didn’t load at all
        Yii::$app->session->setFlash('error', 'Form data not loaded. Check if form fields match model attributes.');
    }

    return $this->render('create', ['model' => $model]);
}


    public function actionIndex($filter = 'all')
{
    $query = Users::find();

    if ($filter !== 'all') {
        $query->where(['role' => ucfirst($filter)]);
    }

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'pagination' => ['pageSize' => 10],
        'sort' => ['defaultOrder' => ['user_id' => SORT_DESC]],
    ]);

    // Prepare privileges for each user
    $auth = Yii::$app->authManager;
    $usersPrivileges = [];
foreach ($dataProvider->getModels() as $user) {
    $permissions = Yii::$app->authManager->getPermissionsByUser($user->user_id);
    $usersPrivileges[$user->user_id] = $permissions; // keep Permission objects
}


    return $this->render('index', [
        'dataProvider' => $dataProvider,
        'filter' => $filter,
        'usersPrivileges' => $usersPrivileges, // pass to view
    ]);
}

public function actionUpdate($id)
{
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post())) {

        // --- Grab privileges from POST ---
        $model->privileges = Yii::$app->request->post('Users')['privileges'] ?? [];

        if ($model->save()) { // save with validation
            $auth = Yii::$app->authManager;

            // --- Revoke all previous roles & permissions ---
            $auth->revokeAll($model->user_id);

            // --- Assign new role ---
            $roleName = $model->role;
            $role = $auth->getRole($roleName);
            if ($role) {
                $auth->assign($role, $model->user_id);
            }

            // --- Assign selected privileges (permissions) ---
            if (!empty($model->privileges) && is_array($model->privileges)) {
                foreach ($model->privileges as $permName) {
                    $permission = $auth->getPermission($permName);
                    if ($permission) {
                        $auth->assign($permission, $model->user_id);
                    }
                }
            }

            Yii::$app->session->setFlash('success', 'User updated successfully!');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update user. Please check the input.');
        }
    }

    // --- Prepare existing privileges for UI checkboxes ---
    $auth = Yii::$app->authManager;
    $assignedPermissions = $auth->getPermissionsByUser($model->user_id);
    $model->privileges = array_keys($assignedPermissions);

    return $this->render('update', ['model' => $model]);
}


    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Remove RBAC roles/permissions
        Yii::$app->authManager->revokeAll($id);

        $model->delete();
        Yii::$app->session->setFlash('success', 'User deleted!');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested user does not exist.');
    }
}