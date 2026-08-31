<?php

namespace app\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    public $confirmPassword;

    private $_user;

    public function __construct($token, $config = [])
    {
        if (empty($token) || !Users::isPasswordResetTokenValid($token)) {
            throw new InvalidArgumentException('Password reset token is invalid or expired.');
        }

        $this->_user = Users::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Password reset token is invalid or expired.');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['password', 'confirmPassword'], 'required'],
            [['password'], 'string', 'min' => 8],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match."],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'New password',
            'confirmPassword' => 'Confirm new password',
        ];
    }

    /**
     * Resets password and invalidates the token.
     *
     * @return bool
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false, ['password_hash', 'password_reset_token']);
    }
}
