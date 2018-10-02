<?php
namespace common\models;

use common\access\{
    AccessInterface, AccessUserInterface, AccessManager
};
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use common\behaviors\{
    ImageBehavior, FileBehavior, ReadOnlyBehavior, TimestampBehavior
};
use common\components\Mailer;
use common\components\Validator\PurifierValidator;
use modules\vacation\models\Vacation;
use modules\country\models\Country;
use modules\registration\models\Registration;
use modules\position\models\Position;
use modules\logposition\models\LogPosition;
use modules\project\models\Project;
use common\models\UserSearch;

/**
 * User model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $avatar
 * @property integer $verified
 * @property integer $active
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_at
 * @property string $password write-only password
 *
 * @property string $imageUrl
 * @property string $imageThumbnailUrl
 * @property RoleModel $roles
 * @property string $roleName
 */
class User extends ActiveRecord implements IdentityInterface, AccessUserInterface
{
    public $password;
    public $confirm_password;
    public $roleListName;
    public $log;

    const DEFAULT_ADMIN_ID = 1;

    const DEFAULT_ROLE = 'employee';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPER_ADMIN = 'super-admin';
    const ROLE_PROJECT_MANAGER = 'project-manager';
    const ROLE_SALES_MANAGER = 'sales-manager';


    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;

    const VERIFIED_NO = 0;
    const VERIFIED_YES = 1;

    const ACTIVE_NO = 0;
    const ACTIVE_YES = 1;

    const DELETED_NO = 0;
    const DELETED_YES = 1;

    const SECRET_BIRTHDAY_ON = 0;
    const SECRET_BIRTHDAY_YES = 1;

    const SCENARIO_SAVE_USER = 'save_user';
    const SCENARIO_SAVE_USER_POSITION = 'save_user_postion';
    const SCENARIO_SAVE_USER_EMPLOYEE = 'save_user_employee';

    const SCENARIO_WITH_PASSWORD = 'withPassword';

    const TAB_NAME_OTHERS = 'others';
    const TAB_NAME_GROUP = 'group';
    const TAB_NAME_PERMISSION = 'permission';
    const TAB_NAME_SLOGANS = 'slogans';
    const TAB_NAME_NETWORKS = 'networks';
    const TAB_NAME_PASSWORD = 'password';
    const TAB_NAME_WORK = 'Work';
    const TAB_NAME_CONTACTS = 'contacts';
    const TAB_NAME_GENERAL = 'general';


    public static function getGenders()
    {
        return [
            self::GENDER_MALE => Yii::t('app', 'Male'),
            self::GENDER_FEMALE => Yii::t('app', 'Female'),
        ];
    }

    public static function getVerifyStatuses()
    {
        return [
            self::VERIFIED_NO => Yii::t('yii', 'No'),
            self::VERIFIED_YES => Yii::t('yii', 'Yes'),
        ];
    }

    public static function getActiveStatuses()
    {
        return [
            self::ACTIVE_NO => Yii::t('yii', 'No'),
            self::ACTIVE_YES => Yii::t('yii', 'Yes'),
        ];
    }

    public static function getDeletedStatuses()
    {
        return [
            self::DELETED_NO => Yii::t('yii', 'No'),
            self::DELETED_YES => Yii::t('yii', 'Yes'),
        ];
    }

    public static function getSecretBirthday()
    {
        return [
            self::SECRET_BIRTHDAY_ON => Yii::t('yii', 'No'),
            self::SECRET_BIRTHDAY_YES => Yii::t('yii', 'Yes'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            ReadOnlyBehavior::className(),
            [
                'class' => FileBehavior::className(),
                'fieldName' => 'resume',
            ],
            [
                'class' => FileBehavior::className(),
                'fieldName' => 'skills',
            ],
            [
                'class' => ImageBehavior::className(),
                'fieldName' => 'avatar',
            ],
        ];
    }

    /**
     * @inheritdocm
     */
    public function rules()
    {
        return [
            [['username', 'email', 'roleListName', 'phone', 'color'], 'required'],
            [['username', 'email', 'first_name', 'last_name', 'middle_name', 'first_name_en', 'last_name_en', 'zip', 'city', 'address', 'phone', 'skype', 'birthday', 'interview', 'date_receipt', 'date_dismissal'], 'trim'],
            [['country_id', 'created_at', 'updated_at', 'last_login_at', 'currency', 'registration_id', 'position_id', 'salary', 'reporting_salary', 'vat'], 'integer'],
            [['phone'], 'integer', 'min' => 10, 'integerOnly' => true,],
            [['vat'], 'integer', 'min' => 10, 'integerOnly' => true,],
            [['gender', 'verified', 'active', 'deleted', 'secret_birthday'], 'boolean'],
            [['username'], 'string', 'min' => 2],
            [['username', 'password_hash', 'password_reset_token', 'access_token', 'email', 'avatar', 'resume', 'skills', 'passport_number'], 'string', 'max' => 255],
            [['note', 'slogan', 'like', 'dislike', 'personal_meeting'], 'string'],
            [['personal_meeting'], 'filter', 'filter' => 'strip_tags'],
            [['note', 'slogan', 'like', 'dislike', 'skype', 'phone', 'englishLevel', 'positionAndGrade', 'performanceAppraisalReview', 'personalDevelopmentPlan'], PurifierValidator::className()],
            [['auth_key'], 'string', 'max' => 32],
            [['first_name', 'last_name', 'middle_name', 'first_name_en', 'last_name_en', 'city', 'address'], 'string', 'max' => 100],
            [['zip'], 'string', 'max' => 10],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token', 'access_token'], 'unique'],
            [['email'], 'email'],
            [['birthday'], 'date', 'format' => 'php:Y-m-d'],
            [['date_receipt'], 'date', 'format' => 'php:Y-m-d'],
            [['date_dismissal'], 'date', 'format' => 'php:Y-m-d'],
            [['interview'], 'date', 'format' => 'php:Y-m-d'],
            [['password_hash'], 'required', 'on' => [self::SCENARIO_WITH_PASSWORD]],
            [['password', 'confirm_password'], 'required',
                'when' => function ($model) {
                    return empty($model->password_hash) && empty($model->password);
                },
                'whenClient' => 'function (attribute, value) { return !(' . (int) !empty($this->password_hash) . ' || value != "") || $("#user-password").val() != ""; }',
            ],
            [['password', 'confirm_password'], 'string', 'min' => 6],
            [['confirm_password'], 'compare', 'compareAttribute' => 'password'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
            [['resume'], 'file', 'skipOnEmpty' => true, 'extensions' => 'txt, doc, docx, pdf'],
            [['skills'], 'file', 'skipOnEmpty' => true, 'extensions' => 'txt, doc, docx, pdf'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['registration_id'], 'exist', 'skipOnError' => true, 'targetClass' => Registration::className(), 'targetAttribute' => ['registration_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'birthday' => Yii::t('app', 'Birthday'),
            'secret_birthday' => Yii::t('app', 'Secret Birthday'),
            'access_token' => Yii::t('app', 'Access Token'),
            'email' => Yii::t('app', 'E-mail'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'first_name_en' => Yii::t('app', 'First Name EN'),
            'last_name_en' => Yii::t('app', 'Last Name EN'),
            'roleListName' => Yii::t('app', 'Group'),
            'avatar' => Yii::t('app', 'Avatar'),
            'verified' => Yii::t('app', 'Verified'),
            'active' => Yii::t('app', 'Active'),
            'created_at' => Yii::t('app', 'Registered'),
            'updated_at' => Yii::t('app', 'Updated'),
            'last_login_at' => Yii::t('app', 'Last Login'),
            'created' => Yii::t('app', 'Registered'),
            'updated' => Yii::t('app', 'Updated'),
            'password' => Yii::t('app', 'Password'),
            'confirm_password' => Yii::t('app', 'Confirm Password'),
            'genderName' => Yii::t('app', 'Gender'),
            'fullName' => Yii::t('app', 'Full Name'),
            'imageThumbnailUrl' => Yii::t('app', 'Avatar'),
            'lastLogin' => Yii::t('app', 'Last Login'),
            'createAt' => Yii::t('app', 'Registered'),
            'position_id' => Yii::t('app', 'Position'),
            'registration_id' => Yii::t('app', 'Registration'),
            'date_receipt' => Yii::t('app', 'Date Receipt'),
            'date_dismissal' => Yii::t('app', 'Date Dismissal'),
            'middle_name' => Yii::t('app', 'Middle Name'),
            'currency' => Yii::t('app', 'Currency'),
            'salary' => Yii::t('app', 'Salary'),
            'reporting_salary' => Yii::t('app', 'Reporting Salary'),
            'country_id' => Yii::t('app', 'Country'),
            'passport_number' => Yii::t('app', 'Passport Number'),
            'vat' => Yii::t('app', 'VAT'),
            'resume' => Yii::t('app', 'Resume'),
            'englishLevel' => Yii::t('app', 'English Level'),
            'skills' => Yii::t('app', 'Skills'),
            'positionAndGrade' => Yii::t('app', 'Position And Grade'),
            'performanceAppraisalReview' => Yii::t('app', 'Performance Appraisal Review'),
            'personalDevelopmentPlan' => Yii::t('app', 'Personal Development Plan'),
            'interview' => Yii::t('app', 'Interview'),
            'note' => Yii::t('app', 'Note'),
            'slogan' => Yii::t('app', 'Slogan'),
            'like' => Yii::t('app', 'Like'),
            'dislike' => Yii::t('app', 'Dislike'),
            'zip' => Yii::t('app', 'Zip'),
            'city' => Yii::t('app', 'City'),
            'address' => Yii::t('app', 'Address'),
            'phone' => Yii::t('app', 'Phone'),
            'skype' => Yii::t('app', 'Skype'),
            'facebook' => Yii::t('app', 'Facebook'),
            'linkedin' => Yii::t('app', 'Linkedin'),
            'gender' => Yii::t('app', 'Gender'),
            'color' => Yii::t('app', 'Color'),
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_SAVE_USER_EMPLOYEE => [
                'username',
                'email',
                'birthday',
                'secret_birthday',
                'auth_key',
                'password_hash',
                'password_reset_token' ,
                'access_token',
                'first_name',
                'last_name',
                'first_name_en',
                'last_name_en',
                'middle_name',
                'currency',
                'vat',
                'slogan',
                'like',
                'dislike',
                'country_id',
                'zip',
                'city',
                'address',
                'phone',
                'skype',
                'facebook',
                'linkedin',
                'avatar',
                'gender',
                'password',
                'confirm_password',
                'genderName',
                'fullName',
                'imageThumbnailUrl',
                'color',
            ],
            self::SCENARIO_SAVE_USER_POSITION => [
                'username',
                'email',
                'auth_key',
                'password_hash',
                'password_reset_token' ,
                'access_token',
                'first_name',
                'last_name',
                'first_name_en',
                'last_name_en',
                'middle_name',
                'currency',
                'roleListName',
                'position_id',
                'salary',
                'reporting_salary',
                'registration_id',
                'passport_number',
                'vat',
                'note',
                'slogan',
                'like',
                'dislike',
                'country_id',
                'zip',
                'city',
                'address',
                'phone',
                'skype',
                'facebook',
                'linkedin',
                'avatar',
                'gender',
                'password',
                'confirm_password',
                'genderName',
                'fullName',
                'imageThumbnailUrl',
                'lastLogin',
                'color',
            ],
            self::SCENARIO_SAVE_USER => [
                'username',
                'email',
                'birthday',
                'secret_birthday',
                'date_receipt',
                'date_dismissal',
                'auth_key',
                'password_hash',
                'password_reset_token' ,
                'access_token',
                'first_name',
                'last_name',
                'first_name_en',
                'last_name_en',
                'middle_name',
                'currency',
                'roleListName',
                'position_id',
                'salary',
                'reporting_salary',
                'registration_id',
                'country_id',
                'passport_number',
                'vat',
                'resume',
                'skills',
                'englishLevel',
                'positionAndGrade',
                'performanceAppraisalReview',
                'personalDevelopmentPlan',
                'interview',
                'note',
                'slogan',
                'like',
                'dislike',
                'zip',
                'city',
                'address',
                'phone',
                'skype',
                'facebook',
                'linkedin',
                'avatar',
                'gender',
                'verified',
                'active',
                'created_at',
                'updated_at',
                'last_login_at',
                'created',
                'updated',
                'password',
                'confirm_password',
                'genderName',
                'fullName',
                'imageThumbnailUrl',
                'lastLogin',
                'color',
                'personal_meeting',
            ],
        ];
    }

    /**
     * Set attribute roleListName
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->roleListName = $this->getRoleListName();
    }

    /**
     * @param bool $insert
     * @param array $attributes
     */
    public function afterSave($insert, $attributes)
    {
        parent::afterSave($insert, $attributes);

        /**
         * Assign role by user;
         */
        if (empty($this->roleListName)) {
            $this->roleListName[] = self::DEFAULT_ROLE;
        }

        Yii::$app->authManager->updateRoleListByUser($this->roleListName, $this);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        /**
         * Remove assigned role for user
         */
        if (!Yii::$app->authManager->revokeAll($this->id)) {
            return false;
        }
        Yii::$app->accessManager->removeAllPermissions($this);

        return true;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'verified' => self::VERIFIED_YES, 'active' => self::ACTIVE_YES, 'deleted' => User::DELETED_NO]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'verified' => self::VERIFIED_YES, 'active' => self::ACTIVE_YES, 'deleted' => User::DELETED_NO]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @return bool
     */
    public function getActiveUser(): bool
    {
        $verified = $this->verified === self::VERIFIED_YES;
        $active = $this->active === self::ACTIVE_YES;
        $deleted = $this->deleted === self::DELETED_NO;

        return $verified && $active && $deleted;
    }



    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'verified' => self::VERIFIED_YES,
            'active' => self::ACTIVE_YES,
            'deleted' => User::DELETED_NO
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates new access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes access token
     */
    public function removeAccessToken()
    {
        $this->access_token = null;
    }

    /**
     * @return boolean
     */
    public function beforeValidate()
    {
        if (!empty($this->password)) {
            $this->setPassword($this->password);
            $this->generateAuthKey();

            if (empty($this->roleListName)) {
                $this->roleListName[] = self::DEFAULT_ROLE;
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @return boolean
     */
    public function afterValidate()
    {
        if (!empty($this->birthday)) {
            if(is_numeric($this->birthday)) {
                $this->birthday = intval($this->birthday);
            } else {
                $this->birthday = strtotime($this->birthday);
            }
        }
        if (!empty($this->interview)) {
            if(is_numeric($this->interview)) {
                $this->interview = intval($this->interview);
            }
            else {
                $this->interview = strtotime($this->interview);
            }
        }
        if (!empty($this->date_receipt)) {
            if(is_numeric($this->date_receipt)) {
                $this->date_receipt = intval($this->date_receipt);
            }
            else {
                $this->date_receipt = strtotime($this->date_receipt);
            }
        }
        if (!empty($this->date_dismissal)) {
            $this->active = self::ACTIVE_NO;
            if(is_numeric($this->date_dismissal)) {
                $this->date_dismissal = intval($this->date_dismissal);
            }
            else {
                $this->date_dismissal = strtotime($this->date_dismissal);
            }

        }

        return parent::afterValidate();
    }

    /**
     * @return boolean
     */
    public function signup()
    {
        $this->generateAccessToken();
        if ($this->save()) {
            return (new Mailer())->sendSignupEmail($this);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function confirmSignup()
    {
        $this->verified = self::VERIFIED_YES;
        $this->removeAccessToken();
        return $this->save();
    }

    /**
     * Resets password.
     *
     * @param string $password
     * @return boolean if password was reset.
     */
    public function resetPassword($password)
    {
        $this->setPassword($password);
        $this->removePasswordResetToken();
        return $this->save(false);
    }

    /**
     * @param bool $active
     * @return bool
     */
    public function setActive($active = true)
    {
        $this->active = $active;
        return $this::save(false);
    }

    /**
     * @return boolean
     */
    public function reverseActive()
    {
        if ($this->active) {
            return $this->setActive(false);
        }
        return $this->setActive();
    }

    /**
     * @param bool $deleted
     * @return bool
     */
    private function setDelete($deleted = false)
    {
        $this->deleted = $deleted;
        $this->scenario = User::SCENARIO_SAVE_USER;
        return $this::save(false);
    }

    /**
     * @return boolean
     */
    public function archive()
    {
        return $this->setDelete(true);
    }

    /**
     * @return boolean
     */
    public function restore()
    {
        return $this->setDelete(false);
    }

    /**
     * @param $user User
     * @return boolean
     */
    public function mergeUser($user)
    {
        if ($this->id == $user->id) {
            return false;
        }

        // Link social acounts
        foreach ($user->auths as $auth) {
            $auth->user_id = $this->id;
            $auth->save();
        }

        $user->delete();

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogRegistration()
    {
        return $this->hasOne(Registration::className(), ['id' => 'registration_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(RoleModel::className(), ['name' => 'item_name'])
            ->viaTable('{{%auth_assignment}}', ['user_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVacation()
    {
        $startYear = mktime(0, 0, 0, 1, 1, date('Y'));//todo
        $endYear = mktime(23, 59, 59, 12, 31, date('Y'));//todo

        return $this->hasMany(Vacation::className(), ['user_id' => 'id'])
//            ->where(['type' => Vacation::TYPE_TARIFF])
            ->andWhere(['between', 'start_at', $startYear, $endYear])
            ->andWhere(['between', 'end_at', $startYear, $endYear]);
    }

    /**
     * Get role name for user (rbac)
     *
     * @return mixed
     */
    public function getRoleName()
    {
        return $this->getRoles()->one()->name;
    }

    public function getRoleListName()
    {
        return $this->getRoles()->all();
    }

    /**
     * @return \yii\rbac\Role[]
     */
    public static function getListRoles()
    {
        return Yii::$app->authManager->getRoles();
    }

    /**
     * @return string
     */
    public function getCreateAt()
    {
        return !empty($this->created_at) ? Yii::$app->formatter->asDate($this->created_at) : 'Not Set';
    }


    /**
     * @return string
     */
    public function getLastLogin()
    {
        return !empty($this->last_login_at) ? Yii::$app->formatter->asDate($this->last_login_at) : 'Not Set';
    }

    /**
     * @return string
     */
    public function getDateReceipt()
    {
        return !empty($this->date_receipt) ? Yii::$app->formatter->asDate($this->date_receipt) : 'Not Set';
    }


    /**
     * @return string
     */
    public function getGenderName()
    {
        return self::getGenders()[$this->gender];
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return string
     */
    public function getFullNameEn()
    {
        return $this->first_name_en . ' ' . $this->last_name_en;
    }

    /**
     * @return string
     */
    public function getHour()
    {
        $user=new UserSearch();
        return $user->getTime($this->id);
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return ($this->country ? $this->country->name : '') . ' ' . $this->city . ' ' . $this->zip;
    }

    /**
     * @return string
     */
    public function getFullAddress()
    {
        return $this->location . ' ' . $this->address;
    }

    /**
     * @return array
     */
    public static function getList()
    {
        $listUser = User::find()->where(['active' => self::ACTIVE_YES])->orderBy(['last_name' => SORT_ASC])->all();

        return ArrayHelper::map(
            $listUser,
            'id',
            function ($user) {
                return $user->last_name . ' ' . $user->first_name . ' ' . $user->middle_name;
            }
        );
    }

    /**
     * @return array
     */
    public static function getListEn()
    {
        $listUser = User::find()->where(['active' => self::ACTIVE_YES])->orderBy(['last_name' => SORT_ASC])->all();

        return ArrayHelper::map(
            $listUser,
            'id',
            function ($user) {
                return $user->last_name_en . ' ' . $user->first_name_en;
            }
        );
    }

    /**
     * @return array
     */
    public static function getListDaveloper()
    {
        $listUser =  User::find()->where([
            'active' => self::ACTIVE_YES,
            'deleted' => User::DELETED_NO,
        ])->orderBy(['last_name' => SORT_ASC])->all();

        return ArrayHelper::map(
            $listUser,
            'id',
            function ($user) {
                return $user->last_name . ' ' . $user->first_name . ' ' . $user->middle_name;
            }
        );
    }

    /**
     * @return array
     */
    public static function getListManager()
    {
        $listUser = User::find()->where([
            'deleted' => User::DELETED_NO,
            'active' => self::ACTIVE_YES,
        ])->orderBy(['last_name' => SORT_ASC])->all();

        return ArrayHelper::map(
            $listUser,
            'id',
            function ($user) {
                return $user->last_name . ' ' . $user->first_name . ' ' . $user->middle_name;
            }
        );
    }


    /**
     * @param $id
     * @return string
     */
    public static function getName($id)
    {
        $user = User::findOne($id);
        return $user->last_name . ' '. $user->first_name . ' ' .  $user->middle_name;
    }

    /**
     * Working experience of an employee
     * @return array
     */
    public function experienceUser()
    {
        $users = User::find()->orderBy(['last_name' => SORT_ASC])->all();
        $year = 3600 * 24 * 365;
        $date = [];
        foreach ($users as $user) {
            $date_receipt = ((!$user->date_receipt) ? time() : $user->date_receipt);
            $receipt = time() - $date_receipt;
            $month = floor($receipt / 2592000) - (intval($receipt / $year) * 12);
            $date[] = [
                'userId' => $user->id,
                'experience' => intval($receipt / $year) . ' ' . Yii::t('app', 'y') . ' ' . $month . ' ' . Yii::t('app', 'm'),
            ];
        }
        $date = ArrayHelper::index($date, 'userId');

        return $date;
    }

    /**
     * @param $log
     */
    public function saveLog($log)
    {
        $this->position_id = (!empty($log[4])? $log[4]: '' );
        $this->registration_id = (!empty($log[10])? $log[10]: '');
        $this->salary = (!empty($log[16])? $log[16]: '');
        $this->reporting_salary = (!empty($log[20])? $log[20]: '');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['id' => 'project_id'])
            ->viaTable(UserProject::tableName(), ['user_id' => 'id']);
    }

    /**
     * @param array|string $name
     *
     * @return bool
     */
    public function isRole($role) : bool
    {
        if (empty($role)) {
            return false;
        }

        if (!is_array($role)) {
            $role = [$role];
        }
        $intersect = array_intersect(array_column($this->roles, 'name'), $role);

        return count($intersect) === count($role);
    }

    /**
     * @return boolean
     */
    public function isSuperAdmin()
    {
        return $this->isRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->isRole(self::ROLE_ADMIN);
    }


    /**
     * @return boolean
     */
    public function isAllowedToViewStage()
    {
        $role_admin = $this->isAdmin();
        $role_super_admin = $this->isSuperAdmin();
        $role_sales_manager = $this->isRole(self::ROLE_SALES_MANAGER);
        $role_project_manager = $this->isRole(self::ROLE_PROJECT_MANAGER);

        return $role_admin || $role_super_admin || $role_sales_manager || $role_project_manager;
    }

    /**
     * Finds the LogPosition model based on its primary key value.
     * @param integer $id
     * @return LogPosition the loaded model
     */
    public function findLogPositionModel($id)
    {
        if (($model = LogPosition::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->all()) !== null) {
            return $model;
        }
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();

        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token'], $fields['access_token']);
        return array_merge($fields, [
            'role' => 'roles',
            'logPosition',
            'country',
            'genderName',
            'avatarUrl' => 'imageThumbnailUrl',
        ]);
    }

    /**
     * @return array
     */
    public static function getMenu()
    {

        $menu = [
            ['label' => Yii::t('app', 'Users'), 'url' => ['/user']],
        ];

        if (Yii::$app->user->can('core.backend.user:' . AccessManager::CREATE)) {
            $menu = array_merge($menu, [
                ['label' => Yii::t('app', 'Add User'), 'url' => ['/user/create']],
            ]);
        }
        $menu = array_merge($menu, [
            ['label' => Yii::t('app', 'Structure'), 'url' => ['/user/structure']],
        ]);

        if (Yii::$app->user->can('position.backend.default:' . AccessManager::UPDATE)) {
            $menu = array_merge($menu, [
                ['label' => Yii::t('app', 'Positions'), 'url' => ['/position']],
            ]);
        }

        if (Yii::$app->user->can('department.backend.default:' . AccessManager::UPDATE)) {
            $menu = array_merge($menu, [
                ['label' => Yii::t('app', 'Departments'), 'url' => ['/department']],
            ]);
        }
        if (Yii::$app->user->can('core.backend.user:' . AccessManager::DELETE)) {
            $menu = array_merge($menu, [
                ['label' => Yii::t('app', 'Trash'), 'url' => ['/user/trash']],
            ]);
        }

        return $menu;
    }

    /**
     * @return ActiveQuery $models
     */
    public function getVacations()
    {
        return $this->hasMany(Vacation::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * @inheritdoc
     */
    public function getUId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getUType(): int
    {
        return AccessInterface::USER;
    }

    /**
     * @inheritdoc
     */
    public function getUParent()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUPrefix(): string
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isUOwner(): bool
    {
        return false;
    }

    /**
     * return owner of this document
     *
     * @return AccessUserInterface
     */
    public function getUOwner()
    {
        return null;
    }


}
