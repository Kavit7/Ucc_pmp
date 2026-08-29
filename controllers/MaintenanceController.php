<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\MaintenanceRequest;
use app\models\Lease;
use app\models\Property;
use app\models\Users;
use app\models\ListSource;
use app\models\Notification;

class MaintenanceController extends Controller
{
    public $layout = 'custom';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    return Yii::$app->response->redirect(['login/login']);
                },
            ],
        ];
    }

    private function isStaff()
    {
        return in_array(Yii::$app->user->identity->role ?? null, ['admin', 'manager', 'technician'], true);
    }

    public function actionIndex()
    {
        $query = MaintenanceRequest::find()->with(['property', 'reportedBy', 'assignedTo', 'status', 'priority']);

        if (!$this->isStaff()) {
            $query->andWhere(['reported_by' => Yii::$app->user->id]);
        }

        $statusId = Yii::$app->request->get('status');
        if ($statusId) {
            $query->andWhere(['status_id' => $statusId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $statusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Maintenance Status'])->scalar();
        $statusOptions = ArrayHelper::map(
            ListSource::find()->where(['parent_id' => $statusParentId])->all(),
            'id',
            'list_Name'
        );

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'statusOptions' => $statusOptions,
            'statusId' => $statusId,
            'isStaff' => $this->isStaff(),
        ]);
    }

    public function actionCreate()
    {
        $model = new MaintenanceRequest();
        $model->reported_by = Yii::$app->user->id;
        $model->status_id = MaintenanceRequest::openStatusId();

        if ($this->isStaff()) {
            $properties = ArrayHelper::map(Property::find()->all(), 'id', 'property_name');
        } else {
            // Tenants can only report against a property they actually lease.
            $properties = ArrayHelper::map(
                Property::find()
                    ->innerJoin('lease', 'lease.property_id = property.id')
                    ->where(['lease.tenant_id' => Yii::$app->user->id])
                    ->distinct()
                    ->all(),
                'id',
                'property_name'
            );
        }

        $priorityParentId = ListSource::find()->select('id')->where(['list_Name' => 'Maintenance Priority'])->scalar();
        $priorityOptions = ArrayHelper::map(
            ListSource::find()->where(['parent_id' => $priorityParentId])->all(),
            'id',
            'list_Name'
        );

        if ($model->load(Yii::$app->request->post())) {
            $model->reported_by = Yii::$app->user->id;

            if (!$this->isStaff() && !array_key_exists($model->property_id, $properties)) {
                throw new ForbiddenHttpException('You can only report issues for properties you lease.');
            }

            $lease = Lease::find()->where(['property_id' => $model->property_id, 'tenant_id' => $model->reported_by])->one();
            $model->lease_id = $lease->id ?? null;

            if ($model->save()) {
                $model->photoFile = UploadedFile::getInstance($model, 'photoFile');
                if ($model->photoFile) {
                    $model->uploadPhoto();
                }

                $propertyName = $model->property->property_name ?? 'a property';
                Notification::notifyRoles(
                    ['admin', 'manager', 'technician'],
                    'New maintenance request',
                    "{$model->title} reported for {$propertyName}.",
                    ['maintenance/update', 'id' => $model->id]
                );

                Yii::$app->session->setFlash('success', 'Maintenance request submitted.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'properties' => $properties,
            'priorityOptions' => $priorityOptions,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$this->isStaff() && $model->reported_by != Yii::$app->user->id) {
            throw new ForbiddenHttpException('You can only view your own requests.');
        }

        if (!$this->isStaff()) {
            // Tenants can view but not change status/assignment.
            return $this->render('view', ['model' => $model]);
        }

        $statusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Maintenance Status'])->scalar();
        $statusOptions = ArrayHelper::map(
            ListSource::find()->where(['parent_id' => $statusParentId])->all(),
            'id',
            'list_Name'
        );
        $technicians = ArrayHelper::map(Users::find()->where(['role' => 'technician'])->all(), 'user_id', 'full_name');

        if (Yii::$app->request->isPost) {
            $oldStatus = $model->status_id;
            $model->status_id = Yii::$app->request->post('status_id', $model->status_id);
            $model->assigned_to = Yii::$app->request->post('assigned_to') ?: null;

            $resolvedStatusId = ListSource::find()->where(['list_Name' => 'Resolved', 'parent_id' => $statusParentId])->select('id')->scalar();
            if ($model->status_id == $resolvedStatusId && $oldStatus != $resolvedStatusId) {
                $model->resolved_at = date('Y-m-d H:i:s');
            }

            if ($model->save(false)) {
                if ($oldStatus != $model->status_id) {
                    $statusName = ListSource::findOne($model->status_id)->list_Name ?? 'updated';
                    Notification::notify(
                        $model->reported_by,
                        'Maintenance request updated',
                        "\"{$model->title}\" is now {$statusName}.",
                        ['maintenance/update', 'id' => $model->id]
                    );
                }
                Yii::$app->session->setFlash('success', 'Request updated.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'statusOptions' => $statusOptions,
            'technicians' => $technicians,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (!$this->isStaff() && $model->reported_by != Yii::$app->user->id) {
            throw new ForbiddenHttpException('You can only view your own requests.');
        }

        return $this->render('view', ['model' => $model]);
    }

    protected function findModel($id)
    {
        if (($model = MaintenanceRequest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested maintenance request does not exist.');
    }
}
