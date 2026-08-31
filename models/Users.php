<?php
namespace app\models;


use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use app\components\AuditLogBehavior;

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

    /**
     * Plaintext password, write-only. Never persisted as-is; beforeSave()
     * hashes it into password_hash. Left empty on update forms to keep
     * the existing password unchanged.
     */
    public $password;

    /**
     * Uploaded profile picture file, write-only. uploadProfilePicture()
     * saves it and sets profile_picture to the resulting web path.
     */
    public $profilePictureFile;

    public static function tableName()
    {
        return 'users';
    }

    public function behaviors()
    {
        return [
            'audit' => ['class' => AuditLogBehavior::class],
        ];
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
            [['full_name', 'email'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 255],
            [['phone', 'profile_picture'], 'string', 'max' => 255],
            [['profilePictureFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 2 * 1024 * 1024],
            [['uuid'], 'unique'],
            [['email'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->password)) {
                $this->password_hash = \Yii::$app->security->generatePasswordHash($this->password);
            }
            if ($insert && empty($this->auth_key)) {
                $this->auth_key = \Yii::$app->security->generateRandomString(32);
            }
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
            ->where(['email' => $username, 'status' => self::STATUS_ACTIVE])
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

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(['password_reset_token' => $token]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600;

        return $timestamp + $expire >= time();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
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
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
      public function setPassword($password){
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Saves profilePictureFile to webroot/uploads, deletes the previous
     * picture (if any), and sets profile_picture to the new relative path.
     */
    public function uploadProfilePicture()
    {
        if (!$this->profilePictureFile instanceof UploadedFile) {
            return true;
        }

        $folder = Yii::getAlias('@webroot/uploads/');
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = Yii::$app->security->generateRandomString() . '.' . $this->profilePictureFile->extension;
        if (!$this->profilePictureFile->saveAs($folder . $fileName)) {
            return false;
        }

        $oldPicture = $this->profile_picture;
        $this->profile_picture = 'uploads/' . $fileName;

        if (!$this->save(false, ['profile_picture'])) {
            return false;
        }

        if ($oldPicture && file_exists(Yii::getAlias('@webroot/' . $oldPicture))) {
            @unlink(Yii::getAlias('@webroot/' . $oldPicture));
        }

        return true;
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
