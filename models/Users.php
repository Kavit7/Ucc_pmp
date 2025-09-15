<?php
namespace app\models;


use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Users extends ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_TECHNICIAN = 'technician';
    const ROLE_ACCOUNTANT = 'accountant';
    const ROLE_TENANT = 'tenant';

    const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';

    public $privileges = [];

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['uuid', 'full_name', 'email'], 'required'],
            [['phone', 'national_id', 'nationality', 'occupation', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['role'], 'default', 'value' => self::ROLE_TENANT],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['role', 'status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['uuid', 'national_id', 'nationality', 'occupation'], 'string', 'max' => 100],
            [['full_name', 'email', 'password'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['uuid'], 'unique'],
            [['email'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        if (is_array($this->privileges)) {
            $this->privileges = json_encode($this->privileges);
        }
        return true;
    }
    return false;
}


    // 🔑 Authentication methods
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['full_name' => $username, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->auth_key ?? null;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // ✅ Hash password check
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }
      public function setPassword($password){
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }
   /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
      public function getUpdatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'updated_by']);
    }
  /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
     public function getCreatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'created_by']);
    }
}
