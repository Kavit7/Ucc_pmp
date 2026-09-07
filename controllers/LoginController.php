<?php

namespace app\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\Users;
use OTPHP\TOTP;

class LoginController extends Controller
{  
    public $layout='login';
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'], // only authenticated users
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('login');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->login();
            if ($result === 'pending_2fa') {
                return $this->redirect(['login/verify2fa']);
            }
            if ($result) {
                return $this->goBack(); // ✅ uses LoginForm::login() which calls Users model
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Second step of login for accounts with two-factor authentication
     * enabled: prompts for a TOTP code and only completes the login
     * (started by LoginForm::login()) once it verifies.
     */
    public function actionVerify2fa()
    {
        $uid = Yii::$app->session->get('2fa_pending_uid');
        $user = $uid ? Users::findOne($uid) : null;

        if (!$user || !$user->totp_enabled) {
            Yii::$app->session->remove('2fa_pending_uid');
            Yii::$app->session->remove('2fa_remember_duration');
            return $this->redirect(['login/login']);
        }

        $error = null;
        if (Yii::$app->request->isPost) {
            $code = trim((string) Yii::$app->request->post('code'));
            $totp = TOTP::createFromSecret($user->totp_secret);
            if ($code !== '' && $totp->verify($code, null, 1)) {
                $duration = (int) Yii::$app->session->get('2fa_remember_duration', 0);
                Yii::$app->session->remove('2fa_pending_uid');
                Yii::$app->session->remove('2fa_remember_duration');
                Yii::$app->user->login($user, $duration);
                return $this->goBack();
            }
            $error = 'That code is incorrect or has expired. Please try again.';
        }

        return $this->render('verify-2fa', ['error' => $error]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionRequestPasswordReset()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'If an account exists for that email, a reset link has been sent.');
                return $this->redirect(['login/login']);
            }

            Yii::$app->session->setFlash('error', 'Could not send the reset email. Please try again later.');
        }

        return $this->render('request-password-reset', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', 'This password reset link is invalid or has expired.');
            return $this->redirect(['login/request-password-reset']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Password reset. You can now log in with your new password.');
            return $this->redirect(['login/login']);
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }
}
