<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Email',
            'password' => 'Password',
            'rememberMe' => 'Remember Me',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Validates credentials and logs in, unless the account has two-factor
     * authentication enabled - in that case login is deferred and the
     * pending state is stashed in session for LoginController::actionVerify2fa()
     * to complete once the user supplies a valid TOTP code.
     *
     * @return bool|string true/false on a normal login attempt, or the
     * string 'pending_2fa' when a code is still required.
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;

            if ($user->totp_enabled) {
                Yii::$app->session->set('2fa_pending_uid', $user->user_id);
                Yii::$app->session->set('2fa_remember_duration', $duration);
                return 'pending_2fa';
            }

            return Yii::$app->user->login($user, $duration);
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Users::findByUsername($this->username);
        }
        return $this->_user;
    }
}
