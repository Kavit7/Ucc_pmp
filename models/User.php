<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord
{
    public $password; 
    public $privileges = []; 
    public static function tableName()
    {
        return 'users';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['full_name', 'email', 'phone', 'national_id', 'nationality', 'occupation', 'role'], 'required', 'message' => 'required'],
            ['email', 'email'],
            [['full_name', 'nationality', 'occupation', 'password'], 'string', 'max' => 255],
            [['phone', 'national_id'], 'string', 'max' => 20],
            [['role'], 'in', 'range' => ['Tenant', 'Manager', 'Admin']],
            [['privileges'], 'safe'], 
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->password)) {
                $this->password= \Yii::$app->security->generatePasswordHash($this->password);
            }
            if (is_array($this->privileges)) {
                $this->privileges = json_encode($this->privileges);
            }
            return true;
        }
        return false;
    }

    public function afterFind()
    {
        parent::afterFind();
        if (!empty($this->privileges)) {
            $this->privileges = json_decode($this->privileges, true);
        }
    }

    
}
