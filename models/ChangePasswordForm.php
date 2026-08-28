<?php
namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['currentPassword', 'newPassword', 'confirmPassword'], 'required'],
            ['confirmPassword', 'compare', 'compareAttribute'=>'newPassword', 'message'=>"Password do not match"],
            ['newPassword', 'string', 'min'=>8, 'tooShort'=>"Nenosiri jipya lazima liwe na angalau herufi 8"],
        ];
    }

    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = Yii::$app->user->identity;

        // Validate current password
        if (!$user->validatePassword($this->currentPassword)) {
            $this->addError('currentPassword', 'Wrong new Password');
            return false;
        }

        // Set new password with hash
        $user->setPassword($this->newPassword);

        if ($user->save(false)) {
            return true;
        }

        return false;
    }
}
