<?php

namespace backend\controllers;

use common\controllers\BaseController;
use common\models\FormGridIds;
use common\models\structure\TreeFabric;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\User;
use common\models\UserSearch;
use common\models\MultipleEMails;
use common\models\MultipleEMailsSearch;
use modules\vacation\models\Vacation;
use modules\position\models\Position;
use modules\department\models\Department;
use modules\logposition\models\LogPositionSearch;
use modules\logposition\models\LogPosition;
use modules\logregistration\models\LogRegistrationSearch;
use modules\logregistration\models\LogRegistration;
use modules\logsalary\models\LogSalarySearch;
use modules\logsalary\models\LogSalary;
use common\models\RoleForm;
use yii\helpers\Json;
use Carbon\Carbon;

use common\access\AccessManager;
use common\access\AccessUserApplicant;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    const WARNING_ROLE_USER = 4;

    public function permissionMapping()
    {
        return [
            'update-role' => 'core.backend.admin:' . AccessManager::UPDATE,
            'index' => AccessManager::VIEW,
            'view' => 'core.backend.admin:' . AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'structure' => AccessManager::VIEW,
            'user-preview' => AccessManager::VIEW,
            'create' => AccessManager::CREATE,
            'archive' => AccessManager::DELETE,
            'activate' => AccessManager::MANAGE,
            'activate-list' => AccessManager::UPDATE,
            'archive-list' => AccessManager::DELETE,
            'delete-image' => AccessManager::DELETE,
            'trash' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
            'restore-list' => AccessManager::UPDATE,
            'restore' => AccessManager::UPDATE,
            'file' => AccessManager::UPDATE,
            'create-multiple-emails' => AccessManager::CREATE,
            'update-multiple-emails' => AccessManager::UPDATE,
            'delete-multiple-emails' => AccessManager::DELETE,

        ];
    }

    private static function getMessages()
    {
        return [
            self::WARNING_ROLE_USER => Yii::t('app', 'Group cannot be blank'),
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $searchModel->active = User::ACTIVE_YES;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all User trash models.
     * @return mixed
     */
    public function actionTrash()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchTrash(Yii::$app->request->queryParams);

        return $this->render('index_trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return Response
     */
    public function actionRestore($id)
    {

        $model = $this->findModel($id);
        $model->restore();

        return $this->redirect('trash');
    }


    /**
     * @return Response
     */
    public function actionRestoreList()
    {
        $ids = Yii::$app->request->post('ids');

        if (! empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $model->restore();
            }
        }

        return $this->redirect('trash');
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User(['scenario' => User::SCENARIO_SAVE_USER]);
        $model->loadDefaultValues();
        $model->verified = true;
        $positions = new LogPosition();

        $roleForm = new RoleForm();
        $permission = Yii::$app->authManager->getPermissions();
        $permissions = $roleForm->getPermissions($permission);
        $rolePermissions = [];
        $registration = new LogRegistration();

        $salary = new LogSalary();

        $randomKey = "ok";
        $post = Yii::$app->request->post('User');
        $model->saveLog(Json::decode($post['log']));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newPermissions = Yii::$app->request->post('Permission');
            $roleForm->removePermission($permission, new AccessUserApplicant($model->id));
            $roleForm->savePermission($newPermissions, new AccessUserApplicant($model->id));
            $positions->saveLogPosition(Json::decode($post['log']), $model->id);
            $registration->saveLogRegistration(Json::decode($post['log']), $model->id);
            $salary->saveLogSalary(Json::decode($post['log']), $model->id);

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'randomKey' => $randomKey,
                'model' => $model,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $params = Yii::$app->request->queryParams['params'];
        $modelVacation = new Vacation();
        $model = $this->findModel($id);
        if ($this->isPermissionUpdateCart($model)) {
            $model->scenario = User::SCENARIO_SAVE_USER_EMPLOYEE;
            if (Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE)) {
                $model->scenario = User::SCENARIO_SAVE_USER;
            }
            $roleForm = new RoleForm();
            $permission = Yii::$app->authManager->getPermissions();
            $permissions = $roleForm->getPermissions($permission);
            $rolePermission = $roleForm->rolePermissionsForUser($permission, $id);

            $searchModelPosition = new LogPositionSearch();
            $dataProviderPosition = $searchModelPosition->search(Yii::$app->request->queryParams);

            $searchModelRegistration = new LogRegistrationSearch();
            $dataProviderRegistration = $searchModelRegistration->search(Yii::$app->request->queryParams);

            $searchModelSalary = new LogSalarySearch();
            $dataProviderSalary = $searchModelSalary->search(Yii::$app->request->queryParams);

            $multipleEMails = new MultipleEMails();
            $multipleEMailsSearch = new MultipleEMailsSearch();
            $dataMultipleEMails = $multipleEMailsSearch->search(Yii::$app->request->queryParams, $id);

            $leftVacation = Vacation::leftVacation($model);
            $fullVacation = Vacation::experienceUser($model);
            $currentDate = Carbon::now();
            $listVacation = Vacation::getAllVacationOrderByDate(
                $model->id,
                $currentDate->copy(),
                [Vacation::TYPE_TARIFF, Vacation::TYPE_NOT_PAID, Vacation::TYPE_SICK_LEAVE]
            );
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $newPermissions = Yii::$app->request->post('Permission');
                $roleForm->removePermission($permission, new AccessUserApplicant($id));
                $roleForm->savePermission($newPermissions, new AccessUserApplicant($id));

                return $this->redirect(array_merge(['index'], $params));
            } else {
                return $this->render('update', [
                    'dataProviderSalary' => $dataProviderSalary,
                    'dataProviderPosition' => $dataProviderPosition,
                    'dataProviderRegistration' => $dataProviderRegistration,
                    'dataMultipleEMails' => $dataMultipleEMails,
                    'multipleEMails' => $multipleEMails,
                    'leftVacation' => $leftVacation,
                    'listVacation' => $listVacation,
                    'modelVacation' => $modelVacation,
                    'model' => $model,
                    'activeTab' => Yii::$app->request->get('tab'),
                    'fullVacation' => $fullVacation,
                    'allVacationList' => $model->vacations,
                    'permissions' => $permissions,
                    'rolePermissions' => $rolePermission,
                ]);
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * View existing user
     *
     * @param integer $id user ID
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Updates role(rbac) by user
     * If update is successful, the browser will be redirected to the 'index' page.
     * @return Response
     */
    public function actionUpdateRole()
    {
        $request = Yii::$app->request;

        if (! $request->isPost) {
            return $this->redirect(['index']);
        }

        $roleListName = $request->post('roleListName');
        $page = $request->post('page') ? $request->post('page') : 1;
        $user = $this->findModel((int)$request->post('id'));
        if (empty($roleListName)) {
            $this->showMessage(self::WARNING_ROLE_USER, 'warning');

            return $this->redirect(array_merge(['index'], ['page' => $page]));
        }

        if (! $user || ! Yii::$app->authManager->updateRoleListByUser($roleListName, $user)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Error update role.'));
        }

        return $this->redirect(array_merge(['index'], ['page' => $page]));
    }

    /**
     * @return string
     */
    public function actionStructure($id = null)
    {
        return $this->render('structure/structure', [
            'id' => $id,
        ]);
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUserPreview()
    {
        $data = Yii::$app->request->post();
        extract($data);
        Yii::$app->response->format = "json";

        if (empty($id)) {
            return [
                "out" => Yii::t('app', 'Something go wrong with extract(). Can\'t find $id'),
                "status" => "error",
            ];
        }

        $idSource = explode('-', $id);

        if (empty($idSource[0]) || empty($idSource[1])) {
            return [
                "out" => Yii::t('app', 'Can\'t parse $id'),
                "status" => "error",
            ];
        }

        $logPosition = array();

        switch ($idSource[0]) {
            case TreeFabric::$keys[TreeFabric::USER_CLASS_NAME]:
                $nodeView = '/user/structure/_userPreview';
                $nodeModel = $this->findModel($idSource[1]);
                $logPosition = $nodeModel->findLogPositionModel($idSource[1]);
                break;
            case TreeFabric::$keys[TreeFabric::POSITION_CLASS_NAME]:
                $nodeView = '/user/structure/_preview';
                if (Yii::$app->user->can('position.backend.default:' . AccessManager::MANAGE)) {
                    $nodeView = '/user/structure/_positionPreview';
                }
                $nodeModel = $this->findPositionModel($idSource[1]);
                break;
            case TreeFabric::$keys[TreeFabric::DEPARTMENT_CLASS_NAME]:
                $nodeView = '/user/structure/_preview';
                if (Yii::$app->user->can('department.backend.default:' . AccessManager::MANAGE)) {
                    $nodeView = '/user/structure/_departmentPreview';
                }
                $nodeModel = $this->findDepartmentModel($idSource[1]);
                break;
        }

        if (empty($nodeModel) || empty($nodeView)) {
            return [
                "out" => Yii::t('app', 'Can\'t find $model and $view'),
                "status" => "error",
            ];
        }

        $params = [
            'node' => $nodeModel,
            'logPosition' => $logPosition,
            'parentKey' => $parentKey,
            'action' => $formAction,
            'formOptions' => empty($formOptions) ? [] : $formOptions,
            'modelClass' => $modelClass,
            'currUrl' => $currUrl,
            'isAdmin' => $isAdmin,
            'iconsList' => $iconsList,
            'softDelete' => $softDelete,
            'showFormButtons' => $showFormButtons,
            'showIDAttribute' => $showIDAttribute,
            'allowNewRoots' => $allowNewRoots,
            'nodeView' => $nodeView,
            'nodeAddlViews' => empty($nodeAddlViews) ? [] : $nodeAddlViews,
            'nodeSelected' => $nodeSelected,
            'breadcrumbs' => empty($breadcrumbs) ? [] : $breadcrumbs,
            'noNodesMessage' => ''
        ];

        return [
            "out" => $this->renderAjax($nodeView, ['params' => $params]),
            "status" => "success",
        ];
    }

    /**
     * Change the active attribute in User model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $model->reverseActive();

        return $this->redirect(['index']);
    }

    /**
     * Change the active attribute in User models.
     *
     * @param bool $active
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionActivateList($active = true)
    {
        $success = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $form = $this->formIds();
        if (! $form) {
            return [
                'success' => false,
            ];
        }

        try {
            foreach ($form->ids as $id) {
                $model = $this->findModel($id);
                $success = $model->setActive($active);
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
            ];
        }

        return [
            'success' => $success,
        ];
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionArchive($id)
    {

        $model = $this->findModel($id);

        $model->archive();

        return $this->redirect(['index']);
    }

    /**
     * Deletes existing User models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionArchiveList()
    {
        $ids = Yii::$app->request->post('ids');
        $success = false;

        if (! empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $model->archive();
                $success = true;
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => $success,
        ];
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['trash']);
    }

    /**
     * @return Response
     */
    public function actionDeleteList()
    {
        $ids = Yii::$app->request->post('ids');

        if (! empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $model->delete();
            }
        }

        return $this->redirect('trash');
    }

    /**
     * Creates a new MultipleEMails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateMultipleEmails($id)
    {
        $model = new MultipleEMails();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'complete';
        } else {
            return $this->renderAjax('multiplemail/_form', [
                'userId' => $id,
                'path' => 'create-multiple-emails',
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MultipleEMails model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdateMultipleEmails($id)
    {
        $model = $this->findModelMultipleEMails($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'complete';
        } else {
            return $this->renderAjax('multiplemail/_form', [
                'path' => 'update-multiple-emails',
                'model' => $model,
            ]);
        }
    }


    /**
     * Deletes an existing MultipleEMails model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDeleteMultipleEmails($id)
    {
        $this->findModelMultipleEMails($id)->delete();

        return 'complete';
    }

    /**
     * Finds the MultipleEMails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return MultipleEMails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelMultipleEMails($id)
    {
        if (($model = MultipleEMails::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Delete image from an existing User model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $success = $this->findModel($id)->deleteImage();
        } catch (\Exception $e) {
            return [
                'success' => false,
            ];
        }

        return [
            'success' => $success,
        ];
    }

    /**
     * Finds the Position model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Position the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPositionModel($id)
    {
        if (($model = Position::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Finds the Department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findDepartmentModel($id)
    {
        if (($model = Department::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Get form ids. Do not use getFormIds to not create new property
     * @return bool|FormGridIds
     */
    protected function formIds()
    {
        $form = new FormGridIds();
        $form->ids = Yii::$app->request->post('ids', []);

        if (! $form->validate()) {
            return false;
        }

        return $form;
    }

    private function isPermissionUpdateCart(User $moduleUser)
    {
        $isPermission = Yii::$app->user->can('core.backend.admin:' . AccessManager::UPDATE);
        $isOwner = $moduleUser->id == Yii::$app->user->identity->id;

        return $isPermission || $isOwner;
    }

    /**
     * @param $id
     * @param $name
     *
     * @return $this
     */
    public function actionFile($id, $name)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException("You cannot preview this attachment");
        }
        $model->fieldName = $name;
        $path = $model->attachmentPath;

        return Yii::$app->response->sendFile($path);
    }

    private function showMessage($messageId, $type = 'error')
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages()[$messageId]]);
    }
}
