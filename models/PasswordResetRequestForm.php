<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    /**
     * Sends a password reset link to the user's email, if an active
     * account with that email exists. Always reports success to the
     * caller regardless of whether the email matched, so this can't be
     * used to enumerate which addresses have accounts.
     *
     * @return bool
     */
    public function sendEmail()
    {
        $user = Users::findOne([
            'email' => $this->email,
            'status' => Users::STATUS_ACTIVE,
        ]);

        if (!$user) {
            return true;
        }

        $user->generatePasswordResetToken();
        if (!$user->save(false, ['password_reset_token'])) {
            return false;
        }

        return Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }
}
