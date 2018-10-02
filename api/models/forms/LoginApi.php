<?php
namespace api\models\forms;

use Yii;
use yii\base\Model;
use common\models\User;


/**
 * Login form
 */
class LoginApi extends Model
{
    public $login;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect login or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return User model whether the user is logged
     * @return bool|User
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            if (!empty($user)) {

                $user->findIdentityByAccessToken($user->access_token);
                return $user;
            }
        }
        return false;
    }

    public function auth()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->scenario = User::SCENARIO_SAVE_USER;
            $user->access_token = Yii::$app->security->generateRandomString();

            return $user->save(false, ['access_token']) ? $user : null;
        } else {
            return false;
        }
    }


    /**
     * Finds user by [[login]]
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->login) ?: User::findByUsername($this->login);
        }

        return $this->_user;
    }
}
