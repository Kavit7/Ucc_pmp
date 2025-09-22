<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\Lease;
use app\models\Bill;
use app\models\ListSource;
use app\models\ChangePasswordForm;
use app\controllers\NotFoundHttpException;
class CustomController extends Controller
{
    public $layout = 'custom';

    /**
     * Access control
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','logout','leases','create-lease','delete-lease','bill','payment','profile','change-password','check-current-password'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?','@'], // logged-in users
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    return $this->redirect(['login/index']);
                },
            ],
        ];
    }

    /**
     * Dashboard
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        return $this->render('index', ['user' => $user]);
    }

    /**
     * Logout
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login/login']);
    }

    /**
     * Create lease
     */

    /**
 * List all leases
 */

public function actionLeases()
{
    $leases = Lease::find()->with(['property','tenant','propertyPrice'])->all();
    return $this->render('leases', [
        'leases' => $leases
    ]);
}

public function actionCreateLease()
{
    $lease = new Lease();

    if ($lease->load(Yii::$app->request->post())) {

        // ✅ Check if the property is already under an active lease
        $activeLease = Lease::find()
            ->where(['property_id' => $lease->property_id])
            ->andWhere(['>=', 'lease_end_date', date('Y-m-d')])
            ->andWhere(['status' => 1]) // 1 = Active
            ->exists();

        if ($activeLease) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'status' => 'error',
                    'message' => 'This property is already leased until its current lease expires.'
                ];
            }

            Yii::$app->session->setFlash('error', 'This property is already leased until its current lease expires.');
            return $this->redirect(['custom/leases']);
        }

        // Handle uploaded lease document
        $lease->lease_doc_file = UploadedFile::getInstance($lease, 'lease_doc_file');
        if ($lease->lease_doc_file instanceof UploadedFile) {
            if (!$lease->uploadDocument()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return [
                        'status' => 'error',
                        'message' => 'Failed to upload lease document.'
                    ];
                }
                Yii::$app->session->setFlash('error', 'Failed to upload lease document.');
                return $this->render('create-lease', ['lease' => $lease]);
            }
        }

        // Generate UUID for Lease (like Property)
        if (empty($lease->uuid)) {
            $lastUuid = Lease::find()
                ->select('uuid')
                ->where(['like', 'uuid', 'Lease_%', false])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();

            $lease->uuid = $lastUuid
                ? 'Lease_' . ((int)str_replace('Lease_', '', $lastUuid) + 1)
                : 'Lease_1';
        }

        // Calculate lease duration in months
        $lease->duration_months = $lease->getDurationMonths();

        // Set created_by / updated_by
        if (!Yii::$app->user->isGuest) {
            $lease->created_by = Yii::$app->user->id;
            $lease->updated_by = Yii::$app->user->id;
        }

        // Save lease
        if ($lease->save(false)) {

    // Automatically create related bill
    $bill = new Bill();
    $bill->uuid = Yii::$app->security->generateRandomString(12);
    $bill->lease_id = $lease->id;
    $bill->amount = ($lease->propertyPrice->unit_amount ?? 0) * ($lease->duration_months ?? 1);
    $bill->due_date = $lease->lease_start_date;
    $bill->created_by = Yii::$app->user->id ?? null;

    // Assign default "Pending" status before saving
    $pending = ListSource::find()
        ->where(['list_Name' => 'Pending', 'category' => 'Bill Status'])
        ->one();
    $bill->bill_status = $pending ? $pending->id : null;

    if ($bill->save(false)) {
        // Bill saved successfully
    }

    if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'status' => 'success',
            'message' => 'Lease and Bill created successfully!'
        ];
    }

    Yii::$app->session->setFlash('success', 'Lease and Bill created successfully!');
    return $this->redirect(['custom/bill']);
}

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'status' => 'error',
                'errors' => $lease->errors
            ];
        }

        Yii::$app->session->setFlash('error', 'Failed to create lease.');
        return $this->render('create-lease', ['lease' => $lease]);
    }

    // GET request: render form
    return $this->render('create-lease', ['lease' => $lease]);
}


    /**
     * Delete lease
     */
    public function actionDeleteLease($id)
    {
        $lease = Lease::findOne($id);
        if ($lease) {
            $lease->delete();
            Yii::$app->session->setFlash('success','Lease deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error','Lease not found.');
        }
        return $this->redirect(['leases']);
    }
 public function actionViewLease($tenant){
    $leases=Lease::find()->where(['tenant_id'=>$tenant])->all();
    $tname=\app\models\Users::find()->where(['user_id'=>$tenant])->one();
    return $this->render('view-lease',[
        'leases'=>$leases,
        'tname'=>$tname,
    ]);
 }
 public function actionRenew($id)
{
    $oldLease = $this->findModel($id);

    $model = new Lease();
    $model->tenant_id = $oldLease->tenant_id;
    $model->property_id = $oldLease->property_id;

    if ($model->load(Yii::$app->request->post())) {
        $model->lease_doc_file = UploadedFile::getInstance($model, 'lease_doc_file');

        if ($model->lease_doc_file instanceof UploadedFile) {
            if (!$model->uploadDocument()) {
                Yii::$app->session->setFlash('error', 'Failed to upload lease document.');
                return $this->render('renew', [
                    'lease' => $model,
                    'oldLease' => $oldLease,
                ]);
            }
        }

        if ($model->save(false)) { 
            Yii::$app->session->setFlash('success', 'Lease renewed successfully.');
            return $this->redirect(['leases', 'id' => $model->id]);
        }
    }

    return $this->render('renew', [
        'lease' => $model,
        'oldLease' => $oldLease,
    ]);
}


    /**
     * View all bills
     */
    public function actionBill()
    {
        $bills = Bill::find()->with('lease')->all();
        return $this->render('bill', ['bills' => $bills]);
    }
    
   public function actionGetPrices($id)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $prices = \app\models\PropertyPrice::find()
        ->where(['property_id' => $id])
        ->all();

    $result = [];
    foreach ($prices as $price) {
        $result[$price->id] = number_format($price->unit_amount, 2);
    }

    return $result;
}

public function actionTerminate($id)
{
  $model = Lease::findOne($id);
if ($model !== null) {
    // pata parent_id ya status yake ya sasa
    $parent = \app\models\ListSource::find()
        ->select(['parent_id'])
        ->where(['id' => $model->status]) // hii inapaswa kuwa status, sio lease->id
        ->scalar(); // inarudisha single value badala ya object

    // pata id ya status yenye jina "Terminated" na parent huyo
    $statusId = \app\models\ListSource::find()
        ->select(['id'])
        ->where(['list_Name' => 'Terminated', 'parent_id' => $parent])
        ->scalar();

    if ($statusId) {
        $model->status = $statusId; // weka id sahihi ya status
        $model->save(false);
    }
}

    return $this->redirect(['leases']); // rudi kwenye list baada ya terminate
}


    public function actionDeleteBill($id)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $bill = Bill::findOne($id);

    if ($bill) {
        $bill->delete();
        return ['status' => 'success', 'message' => 'Bill deleted successfully.'];
    }

    return ['status' => 'error', 'message' => 'Bill not found.'];
}

    /**
     * User profile
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        return $this->render('profile', ['user' => $user]);
    }

    /**
     * Inline AJAX check for current password
     */
    public function actionCheckCurrentPassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $password = $data['currentPassword'] ?? '';

        $user = Yii::$app->user->identity;
        $isValid = $user && $user->validatePassword($password);

        return ['valid' => $isValid];
    }

    /**
     * Change password
     */
    public function actionChangePassword()
{
    $model = new ChangePasswordForm();

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $user = Yii::$app->user->identity;

        // Hakikisha current password ni sahihi
        if (!$user->validatePassword($model->currentPassword)) {
            Yii::$app->session->setFlash('error', 'Current password is incorrect.');
            return $this->refresh();
        }

        // Badilisha password kwa hash
        $user->setPassword($model->newPassword);

        // Huu mstari unaondoa authKey
        // $user->authKey = Yii::$app->security->generateRandomString();

        $user->save(false); // bypass validation, save immediately

        Yii::$app->session->setFlash('success', 'Password changed successfully. Please login again.');
        Yii::$app->user->logout();

        return $this->redirect(['login/login']);
    }

    return $this->render('change-password', ['model' => $model]);
}


    /**
     * Payments view
     */
  /* public function actionPayment()
    {
        $payments = Bill::find()->where(['not',['paid_date'=>null]])->with('lease')->all();
        return $this->render('payment', ['payments' => $payments]);
    }*/

    public function actionPayment()
{
    $payments = Bill::find()->with('lease')->all(); // inarudisha zote bila kuchuja
    return $this->render('payment', ['payments' => $payments]);
}
protected function findModel($id)
    {
        if (($model = Lease::findOne($id)) !== null) {
            return $model;
        }
    }

}