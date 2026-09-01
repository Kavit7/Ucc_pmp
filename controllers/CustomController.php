<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\Lease;
use app\models\Bill;
use app\models\PropertyPrice;
use app\models\ListSource;
use app\models\ChangePasswordForm;
use app\models\Notification;
use yii\data\ActiveDataProvider;
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
                'only' => ['index','logout','leases','create-lease','delete-lease','bill','payment','record-payment','manage-deposit','profile','upload-profile-picture','change-password','check-current-password','notifications','read-notification','mark-all-notifications-read','settings','update-settings','inquiries','view-lease','renew','get-prices','terminate','delete-bill'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create-lease'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $role = Yii::$app->user->identity->role ?? null;
                            return in_array($role, ['admin', 'manager'], true) || Yii::$app->user->can('assign');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-lease', 'record-payment', 'manage-deposit', 'inquiries', 'renew', 'get-prices', 'terminate', 'delete-bill'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return in_array(Yii::$app->user->identity->role ?? null, ['admin', 'manager'], true);
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view-lease'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $role = Yii::$app->user->identity->role ?? null;
                            if (in_array($role, ['admin', 'manager'], true)) {
                                return true;
                            }
                            // Tenants may only view their own leases.
                            return (int) Yii::$app->request->get('tenant') === (int) Yii::$app->user->id;
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'logout', 'leases', 'bill', 'payment', 'profile', 'upload-profile-picture', 'change-password', 'check-current-password', 'notifications', 'read-notification', 'mark-all-notifications-read', 'settings', 'update-settings'],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    return $this->redirect(['login/index']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'read-notification' => ['post'],
                    'mark-all-notifications-read' => ['post'],
                    'delete-lease' => ['post'],
                    'record-payment' => ['get', 'post'], // GET shows the form, POST submits it
                    'manage-deposit' => ['get', 'post'],
                    'upload-profile-picture' => ['post'],
                    'terminate' => ['post'],
                    'delete-bill' => ['post'],
                ],
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
    $query = Lease::find()->with(['property','tenant','propertyPrice']);
    if ((Yii::$app->user->identity->role ?? null) === 'tenant') {
        $query->andWhere(['tenant_id' => Yii::$app->user->id]);
    }
    $leases = $query->all();
    return $this->render('leases', [
        'leases' => $leases
    ]);
}

public function actionCreateLease()
{
    $lease = new Lease();

    // Allow linking here from a property's detail page with the property preselected.
    if (Yii::$app->request->isGet && Yii::$app->request->get('property_id')) {
        $lease->property_id = (int) Yii::$app->request->get('property_id');
    }

    if ($lease->load(Yii::$app->request->post())) {

        // If the selected property has no price on record yet, the form lets
        // the user enter one inline instead of leaving to the (admin-only)
        // property prices page first.
        $newPriceAmount = Yii::$app->request->post('new_price_amount');
        if (empty($lease->property_price_id) && $newPriceAmount !== null && $newPriceAmount !== '') {
            $newPrice = new PropertyPrice();
            $newPrice->property_id = $lease->property_id;
            $newPrice->unit_amount = $newPriceAmount;
            $newPrice->price_type = Yii::$app->request->post('new_price_type') ?: null;
            $newPrice->created_by = Yii::$app->user->id ?? null;

            if ($newPrice->save()) {
                $lease->property_price_id = $newPrice->id;
            } else {
                Yii::$app->session->setFlash('error', 'Could not save the new price: ' . implode(' ', $newPrice->getFirstErrors()));
                return $this->render('create-lease', ['lease' => $lease]);
            }
        }

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

        // Empty deposit field posts as '' - normalize to null instead of
        // letting an empty string hit the decimal column, and default the
        // status to "Held" whenever a deposit amount was actually entered.
        $lease->security_deposit_amount = $lease->security_deposit_amount !== '' && $lease->security_deposit_amount !== null
            ? $lease->security_deposit_amount
            : null;
        if ($lease->security_deposit_amount !== null) {
            $heldStatus = ListSource::find()
                ->where(['list_Name' => 'Held', 'category' => 'Security Deposit Status'])
                ->select('id')
                ->scalar();
            $lease->security_deposit_status = $heldStatus ?: null;
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

    $propertyName = $lease->property->property_name ?? 'a property';
    Notification::notify(
        $lease->tenant_id,
        'Lease created',
        "Your lease for {$propertyName} has been created.",
        ['custom/leases']
    );
    Notification::notifyRoles(
        ['admin', 'manager'],
        'New lease created',
        "{$propertyName} was leased to " . ($lease->tenant->full_name ?? 'a tenant') . '.',
        ['custom/leases']
    );

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
 public function actionManageDeposit($id)
 {
    $lease = Lease::findOne($id);
    if (!$lease) {
        throw new \yii\web\NotFoundHttpException('Lease not found.');
    }

    if (Yii::$app->request->isPost) {
        $lease->security_deposit_amount = Yii::$app->request->post('security_deposit_amount');
        $lease->security_deposit_amount = $lease->security_deposit_amount !== '' ? $lease->security_deposit_amount : null;
        $lease->security_deposit_status = Yii::$app->request->post('security_deposit_status') ?: null;
        $returnedAt = Yii::$app->request->post('security_deposit_returned_at');
        $lease->security_deposit_returned_at = $returnedAt !== '' ? $returnedAt : null;
        $lease->security_deposit_notes = Yii::$app->request->post('security_deposit_notes');
        $lease->updated_by = Yii::$app->user->id ?? null;

        if ($lease->save(false, ['security_deposit_amount', 'security_deposit_status', 'security_deposit_returned_at', 'security_deposit_notes', 'updated_by', 'updated_at'])) {
            Yii::$app->session->setFlash('success', 'Security deposit updated.');
        } else {
            Yii::$app->session->setFlash('error', 'Could not update the security deposit.');
        }
        return $this->redirect(['leases']);
    }

    return $this->render('manage-deposit', [
        'lease' => $lease,
    ]);
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

        // Needed for the bill amount below - actionCreateLease sets this
        // the same way, but this action was missing it entirely.
        $model->duration_months = $model->getDurationMonths();

        if ($model->save(false)) {
            // Close out the old lease so it stops counting as "active"
            // (double-counted occupancy/dashboard stats, and blocked new
            // leases on this property via the active-lease check).
            $renewedStatusId = ListSource::find()
                ->where(['list_Name' => 'Renewed', 'category' => 'Lease Status'])
                ->select('id')
                ->scalar();
            if ($renewedStatusId) {
                $oldLease->status = $renewedStatusId;
                $oldLease->save(false);
            }

            // Auto-create the bill for the renewed term (actionCreateLease
            // does this for a brand-new lease; renewal was skipping it).
            $bill = new Bill();
            $bill->uuid = Yii::$app->security->generateRandomString(12);
            $bill->lease_id = $model->id;
            $bill->amount = ($model->propertyPrice->unit_amount ?? 0) * ($model->duration_months ?? 1);
            $bill->due_date = $model->lease_start_date;
            $bill->created_by = Yii::$app->user->id ?? null;

            $pending = ListSource::find()
                ->where(['list_Name' => 'Pending', 'category' => 'Bill Status'])
                ->one();
            $bill->bill_status = $pending ? $pending->id : null;
            $bill->save(false);

            $propertyName = $model->property->property_name ?? 'a property';
            Notification::notify(
                $model->tenant_id,
                'Lease renewed',
                "Your lease for {$propertyName} has been renewed.",
                ['custom/leases']
            );
            Notification::notifyRoles(
                ['admin', 'manager'],
                'Lease renewed',
                "{$propertyName} lease renewed for " . ($model->tenant->full_name ?? 'a tenant') . '.',
                ['custom/leases']
            );

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
        $query = Bill::find();
        if ((Yii::$app->user->identity->role ?? null) === 'tenant') {
            $query->joinWith('lease')->andWhere(['lease.tenant_id' => Yii::$app->user->id]);
        } else {
            $query->with('lease');
        }
        $bills = $query->all();
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
     * Upload/replace the current user's profile picture.
     */
    public function actionUploadProfilePicture()
    {
        $user = Yii::$app->user->identity;
        $user->profilePictureFile = UploadedFile::getInstanceByName('profilePictureFile');

        if ($user->profilePictureFile && $user->validate(['profilePictureFile']) && $user->uploadProfilePicture()) {
            Yii::$app->session->setFlash('success', 'Profile picture updated.');
        } else {
            $errors = $user->getFirstErrors();
            Yii::$app->session->setFlash('error', $errors ? reset($errors) : 'Please choose a valid image (png, jpg, jpeg, max 2MB).');
        }

        return $this->redirect(['custom/profile']);
    }

    /**
     * Inquiries submitted through the public property listing.
     */
    public function actionInquiries()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\PropertyInquiry::find()->with('property')->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 30],
        ]);

        return $this->render('inquiries', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Full notifications list for the current user.
     */
    public function actionNotifications()
    {
        Notification::syncOverdueBillNotifications();

        $dataProvider = new ActiveDataProvider([
            'query' => Notification::find()->where(['user_id' => Yii::$app->user->id]),
            'pagination' => ['pageSize' => 15],
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        return $this->render('notifications', ['dataProvider' => $dataProvider]);
    }

    /**
     * Marks a single notification read and follows its link, if any.
     */
    public function actionReadNotification($id)
    {
        $notification = Notification::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($notification) {
            $notification->markRead();
            if ($notification->link) {
                return $this->redirect($notification->link);
            }
        }
        return $this->redirect(['custom/notifications']);
    }

    /**
     * Marks all of the current user's notifications read.
     */
    public function actionMarkAllNotificationsRead()
    {
        Notification::updateAll(['is_read' => 1], ['user_id' => Yii::$app->user->id, 'is_read' => 0]);
        return $this->redirect(Yii::$app->request->referrer ?: ['custom/notifications']);
    }

    /**
     * Account settings: notification preference + quick links.
     */
    public function actionSettings()
    {
        return $this->render('settings', ['user' => Yii::$app->user->identity]);
    }

    /**
     * Save account settings.
     */
    public function actionUpdateSettings()
    {
        $user = Yii::$app->user->identity;
        $user->notifications_enabled = (int) Yii::$app->request->post('notifications_enabled', 0);
        $user->save(false, ['notifications_enabled']);

        Yii::$app->session->setFlash('success', 'Settings saved.');
        return $this->redirect(['custom/settings']);
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
     * Payments view: recorded payments only (bills already marked Paid).
     */
    public function actionPayment()
    {
        $paidId = $this->billStatusId('Paid');

        $query = Bill::find()
            ->with(['lease.property', 'lease.tenant'])
            ->where(['bill_status' => $paidId])
            ->orderBy(['paid_date' => SORT_DESC]);

        if ((Yii::$app->user->identity->role ?? null) === 'tenant') {
            $query->joinWith('lease')->andWhere(['lease.tenant_id' => Yii::$app->user->id]);
        }

        $payments = $query->all();

        return $this->render('payment', ['payments' => $payments]);
    }

    /**
     * Record a payment against a pending bill: sets paid_date, moves the
     * bill to "Paid", and optionally stores a receipt.
     */
    public function actionRecordPayment($id)
    {
        $bill = Bill::findOne($id);
        if (!$bill) {
            throw new NotFoundHttpException('Bill not found.');
        }

        if (Yii::$app->request->isPost) {
            $paidDate = Yii::$app->request->post('paid_date') ?: date('Y-m-d');
            $receiptFile = UploadedFile::getInstanceByName('receiptFile');

            if ($receiptFile) {
                $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf'];
                if (!in_array(strtolower($receiptFile->extension), $allowedExtensions, true)) {
                    Yii::$app->session->setFlash('error', 'Receipt must be a png, jpg, jpeg, or pdf file.');
                    return $this->redirect(['custom/bill']);
                }

                $folder = Yii::getAlias('@webroot/uploads/');
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }
                $fileName = Yii::$app->security->generateRandomString() . '.' . $receiptFile->extension;
                if ($receiptFile->saveAs($folder . $fileName)) {
                    $bill->receipt_url = 'uploads/' . $fileName;
                }
            }

            $bill->paid_date = $paidDate;
            $bill->bill_status = $this->billStatusId('Paid');

            if ($bill->save(false)) {
                $propertyName = $bill->lease->property->property_name ?? 'a property';
                $amount = number_format($bill->amount, 2);

                Notification::notify(
                    $bill->lease->tenant_id ?? null,
                    'Payment recorded',
                    "Your payment of TZS {$amount} for {$propertyName} has been recorded.",
                    ['custom/payment']
                );
                Notification::notifyRoles(
                    ['admin', 'manager'],
                    'Payment recorded',
                    "TZS {$amount} payment recorded for {$propertyName}.",
                    ['custom/payment']
                );

                Yii::$app->session->setFlash('success', 'Payment recorded successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to record payment.');
            }

            return $this->redirect(['custom/bill']);
        }

        return $this->render('record-payment', ['bill' => $bill]);
    }

    private function billStatusId($name)
    {
        $parentId = ListSource::find()->select('id')->where(['list_Name' => 'Bill Status'])->scalar();
        return ListSource::find()->where(['list_Name' => $name, 'parent_id' => $parentId])->select('id')->scalar();
    }
protected function findModel($id)
    {
        if (($model = Lease::findOne($id)) !== null) {
            return $model;
        }
    }

}