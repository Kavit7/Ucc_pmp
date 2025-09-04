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
            ['confirmPassword', 'compare', 'compareAttribute'=>'newPassword', 'message'=>"Nenosiri jipya na uthibitisho wake hazifanani"],
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
        if (!Yii::$app->security->validatePassword($this->currentPassword, $user->password_hash)) {
            $this->addError('currentPassword', 'Nenosiri la sasa si sahihi');
            return false;
        }

        // Set new password with hash
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->newPassword);

        if ($user->save(false)) {
            return true;
        }

        return false;
    }
}
