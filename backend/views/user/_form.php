<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\jui\DatePicker;
use common\models\User;
use kartik\file\FileInput;
use modules\country\models\Country;
use modules\logposition\models\LogPosition;
use modules\position\models\Position;
use modules\logsalary\models\LogSalary;
use modules\registration\models\Registration;
use modules\logregistration\models\LogRegistration;

use common\access\AccessManager;

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var yii\widgets\ActiveForm $form */

$session = Yii::$app->session;
$filterDate = $session->get('filterDate');
$userAgeLimit  = 100;
$datePickerRange = (date('Y') - $userAgeLimit) . ':' . date('Y');
?>

<div class="user-form col-lg-8 col-md-12 col-sm-12 alert alert-info">

    <?=  $this->render('_form_modal')?>
    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal',
        ]
    ]); ?>

        <?php $activeTab = !empty($activeTab) ? $activeTab : 'general'; ?>
        <?= ($model->isNewRecord  ?  $form->field($model, 'log', ['options' => ['class' => 'hidden']])->textarea() : '') ; ?>
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app', 'General'),
                    'active' => empty($activeTab) || $activeTab == User::TAB_NAME_GENERAL,
                    'options' => ['id' => 'general'],
                    'content' => $this->render('_avatar', ['form' => $form, 'model' => $model])
                        . $form->field($model, 'username')->textInput(['maxlength' => true])
                        . $form->field($model, 'email')->textInput(['maxlength' => true/*,'value'=>($model->isNewRecord  ? $filterDate['email']: $model->email)*/])
                        . $form->field($model, 'first_name')->textInput(['maxlength' => true])
                        . $form->field($model, 'first_name_en')->textInput(['maxlength' => true])
                        . $form->field($model, 'last_name')->textInput(['maxlength' => true])
                        . $form->field($model, 'last_name_en')->textInput(['maxlength' => true])
                        . $form->field($model, 'middle_name')->textInput(['maxlength' => true])

                        . $form->field($model, 'birthday', ['options' => ['class' => 'hidden']])->textInput(['value' => !empty($model->birthday) ? date('Y-m-d', $model->birthday) : null])
                        . $form->field($model, 'birthday')->widget(DatePicker::classname(), [
                            'clientOptions' => ['changeMonth' => true, 'changeYear' => true, 'yearRange' => $datePickerRange, 'altFormat' => 'yy-mm-dd', 'altField' => '#user-birthday'],
                            'options' => ['class' => 'form-control', 'readonly' => true, 'id' => 'date-picker-birthday', 'name' => 'date-picker-birthday']
                        ])
                        . $form->field($model, 'secret_birthday')->dropdownList(User::getSecretBirthday())

                        . $form->field($model, 'passport_number')->textInput()
                        . $form->field($model, 'vat')->textInput(['type'=>'number','step'=>'any'])
                        . $form->field($model, 'gender')->dropdownList(User::getGenders())
                        . (!$model->isNewRecord  ?
                            $form->field($model, 'created')->textInput(['disabled' => true])
                            . $form->field($model, 'updated')->textInput(['disabled' => true])
                            . $form->field($model, 'lastLogin')->textInput(['disabled' => true])
                            : '')
                ],
                [
                    'label' => Yii::t('app', 'Contacts'),
                    'active' => $activeTab == User::TAB_NAME_CONTACTS,
                    'options' => ['id' => 'contacts'],
                    'content' => $form->field($model, 'country_id')->dropdownList(Country::getList(), ['prompt'=>'','value'=>'232'])
                        . $form->field($model, 'city')->textInput(['maxlength' => true])
                        . $form->field($model, 'zip')->textInput(['maxlength' => true])
                        . $form->field($model, 'address')->textInput(['maxlength' => true])
                        . $form->field($model, 'phone')->textInput(['maxlength' => true])
                        . $form->field($model, 'skype')->textInput(['maxlength' => true])
                        . $form->field($model, 'facebook',[
                            'template' => '{label}<div class="col-sm-10">{input}{error}{hint}</div>',
                            'labelOptions' => ['class' => 'control-label  col-sm-2 '],
                        ])->textInput(['maxlength' => true])->label('<img src="/images/u120.png" alt="/images/u120.png">')
                        . $form->field($model, 'linkedin',[
                            'template' => '{label}<div class="col-sm-10">{input}{error}{hint}</div>',
                            'labelOptions' => ['class' => 'control-label  col-sm-2 '],
                        ])->textInput(['maxlength' => true])->label('<img src="/images/u126.png" alt="/images/u126.png">')
                ],
                [
                    'label' => Yii::t('app', 'Work'),
                    'active' => $activeTab == User::TAB_NAME_WORK,
                    'options' => ['id' => 'settings'],
                    'content' => $this->render('_work', (!$model->isNewRecord ? [
                        'model' => $model,
                        'dataProviderPosition' => $dataProviderPosition,
                        'dataProviderRegistration' => $dataProviderRegistration,
                        'dataProviderSalary' => $dataProviderSalary,
                        'form' => $form,
                    ] : [
                        'model' => $model,
                        'form' => $form
                    ])),
                ],
                [
                    'label' => Yii::t('app', 'Password'),
                    'active' => $activeTab == User::TAB_NAME_PASSWORD,
                    'options' => ['id' => 'password'],
                    'content' => $form->field($model, 'password')->passwordInput()
                        . $form->field($model, 'confirm_password')->passwordInput()
                ],
                [
                    'label' => Yii::t('app', 'Networks'),
                    'active' => $activeTab == User::TAB_NAME_NETWORKS,
                    'visible' => !$model->isNewRecord,
                    'options' => ['id' => 'networks'],
                    'content' => $this->render('_social', ['model' => $model])
                ],
                [
                    'label' => Yii::t('app', 'Slogans'),
                    'active' => $activeTab == User::TAB_NAME_SLOGANS,
                    'options' => ['id' => 'slogans'],
                    'content' => $form->field($model, 'slogan')->textarea(['rows' => 6])
                        . $form->field($model, 'like')->textarea(['rows' => 6])
                        . $form->field($model, 'dislike')->textarea(['rows' => 6])
                ],
                [
                    'label' => Yii::t('app', 'Group'),
                    'active' => $activeTab == User::TAB_NAME_GROUP,
                    'visible' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE),
                    'options' => ['id' => 'group'],
                    'content' => $this->render('_role', ['form' => $form, 'model' => $model]),
                ],
                [
                    'label' => Yii::t('app', 'Permission'),
                    'active' => $activeTab == User::TAB_NAME_PERMISSION,
                    'visible' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE),
                    'options' => ['id' => 'permission'],
                    'content' => $this->render('_permission', [
                        'permissions' => $permissions,
                        'rolePermissions' => $rolePermissions,
                    ]),
                ],
                [
                    'label' => Yii::t('app', 'Vacation'),
                    'active' => $activeTab == 'vacation',
                    'visible' => !$model->isNewRecord,
                    'options' => ['id' => 'vacation'],
                    'content' => !$model->isNewRecord ? $this->render('_vacation_form', [
                        'userId' => Yii::$app->request->get('id'),
                        'leftVacation' => $leftVacation,
                        'listVacation' => $listVacation,
                        'allVacationList' => $allVacationList,
                        'fullVacation' => $fullVacation,
                    ]) : '',
                ],
                [
                    'label' => Yii::t('app', 'Email'),
                    'active' => $activeTab == 'email',
                    'visible' => Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE) ?  !$model->isNewRecord : false,
                    'options' => ['id' => 'email'],
                    'content' => !$model->isNewRecord ? $this->render('multiplemail/_form_email',[
                        'dataProvider' => $dataMultipleEMails,
                        'multipleEMails' => $multipleEMails,
                        ]) : '',
                ],
                [
                    'label' => Yii::t('app', 'Employee Assessment'),
                    'active' => $activeTab == 'skills',
                    'options' => ['id' => 'skills'],
                    'content' =>$this->render('_form_skills', ['form' => $form, 'model' => $model]),
                ],
                [
                    'label' => Yii::t('app', 'Others'),
                    'active' => $activeTab === User::TAB_NAME_OTHERS,
                    'options' => ['id' => 'others'],
                    'content' => $this->render('_others', ['form' => $form, 'model' => $model]),
                ],
            ],
        ]);?>

        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-success', 'id' => 'createUser']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
</div>
