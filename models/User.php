<?php
<<<<<<< HEAD
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Table name
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * Find user by ID (required by IdentityInterface)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 10]);
    }

    /**
     * Not implementing token login
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
=======

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        return null;
    }

    /**
<<<<<<< HEAD
     * Find by username
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => 10]);
    }

    /**
     * IdentityInterface methods
=======
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
     */
    public function getId()
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
=======
    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
    }
}
