<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class CustomController extends Controller
{
    public $layout = 'custom'; // layout ya dashboard

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'logout'], // block dashboard and allow logout only for logged-in users
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // login required
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    // redirect guest to login page
                    return Yii::$app->response->redirect(['login/index']);
                },
            ],
        ];
    }

    public function actionLeases()
{
    // fetch all leases
    $leases = \app\models\Lease::find()->with(['property','tenant','propertyPrice'])->all();

    return $this->render('leases', [
        'leases' => $leases,
    ]);
}


// Create Lease action
public function actionCreateLease()
{
    $lease = new \app\models\Lease();

    if ($lease->load(Yii::$app->request->post()) && $lease->save()) {
        Yii::$app->session->setFlash('success', 'Lease created successfully.');
        return $this->redirect(['view-leases']); // redirect baada ya create
    }

    return $this->render('create-lease', [
        'lease' => $lease,
    ]);
}



public function actionDeleteLease($id)
{
    $lease = \app\models\Lease::findOne($id);
    if ($lease) {
        $lease->delete();
        Yii::$app->session->setFlash('success', 'Lease deleted successfully.');
    } else {
        Yii::$app->session->setFlash('error', 'Lease not found.');
    }
    return $this->redirect(['leases']);
}


    // Dashboard action
    public function actionIndex()
    {
        $user = Yii::$app->user->identity; // info ya logged-in admin
        return $this->render('index', [
            'user' => $user,
        ]);
    }

    // Logout action
    public function actionLogout()
    {
        Yii::$app->user->logout(); // remove session
        return $this->redirect(['login/index']); // redirect to login page
    }
}
