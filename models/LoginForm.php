<?php
<<<<<<< HEAD
=======

>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
namespace app\models;

use Yii;
use yii\base\Model;
<<<<<<< HEAD
use app\models\User;

=======

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

<<<<<<< HEAD
    /**
     * Validation rules
=======

    /**
     * @return array the validation rules.
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
     */
    public function rules()
    {
        return [
<<<<<<< HEAD
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
=======
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
            ['password', 'validatePassword'],
        ];
    }

    /**
<<<<<<< HEAD
     * Validate password
=======
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
<<<<<<< HEAD
            if (!$user || !Yii::$app->getSecurity()->validatePassword($this->password, $user->password_hash)) {
=======

            if (!$user || !$user->validatePassword($this->password)) {
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
<<<<<<< HEAD
     * Login user
=======
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
     */
    public function login()
    {
        if ($this->validate()) {
<<<<<<< HEAD
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600*24*30 : 0
            );
=======
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        }
        return false;
    }

    /**
<<<<<<< HEAD
     * Get user by username
     */
    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['username' => $this->username, 'status' => 10]);
        }
=======
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        return $this->_user;
    }
}
