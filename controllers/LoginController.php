<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;

class LoginController extends Controller
{
    public $layout = 'login'; // layout ya login page

    public function actionIndex()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // admin pekee, redirect dashboard
            return $this->redirect(['custom/index']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }


    public function actionTestLogin()
{
    $username = 'admin';
    $password = 'admin123';

    $user = \app\models\User::findOne(['username' => $username, 'status' => 10]);

    if (!$user) {
        return "Admin not found!";
    }

    if (\Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
        return "Password correct, login works!";
    } else {
        return "Password incorrect!";
    }
}

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
