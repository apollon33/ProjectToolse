<?php

namespace backend\controllers;


use common\access\AccessChainsDependencies;
use common\access\AccessManager;
use common\access\AccessRoleApplicant;
use common\controllers\BaseController;
use common\models\RoleForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;


/**
 * RoleController.
 */
class RoleController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
        ];
    }
    /**
     * Lists all Roles.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => Yii::$app->authManager->getRoles(),
            'sort' => [
                'attributes' => ['name', 'description'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Role.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $permissions = Yii::$app->authManager->getPermissions();
        $roleForm = new RoleForm(['scenario' => RoleForm::IS_NEW_SCENARIO]);
        $permissions = $roleForm->getPermissions($permissions);

        $post = Yii::$app->request->post();

        if ($roleForm->load($post) && $roleForm->validate()) {
            $role = Yii::$app->authManager->createRole($roleForm->name);
            $role->description = $roleForm->description;
            $createRole = Yii::$app->authManager->add($role);

            if ($createRole) {
                $newPermissions =  Yii::$app->request->post('Permission');
                $roleForm->savePermission($newPermissions, new AccessRoleApplicant($roleForm->name));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Error create role.'));
            }
        }

        return $this->render('create', [
            'model' => $roleForm,
            'permissions' => $permissions,
            'rolePermissions' => [],
        ]);
    }

    /**
     * Updates an existing Role.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($name)
    {
        $role = Yii::$app->authManager->getRole($name);
        $roleForm = new RoleForm();

        $permission = Yii::$app->authManager->getPermissions();
        $permissions = $roleForm->getPermissions($permission);
        $rolePermission = $roleForm->rolePermission($permission, new AccessRoleApplicant($name));

        $post = Yii::$app->request->post();

        if ($roleForm->load($post) && $roleForm->validate()) {
            $role->name = $roleForm->name;
            $role->description = $roleForm->description;
            $newPermissions = Yii::$app->request->post('Permission');
            $roleForm->removePermission($permission, new AccessRoleApplicant($name));
            $roleForm->savePermission($newPermissions, new AccessRoleApplicant($name));

            return $this->redirect(['index']);
        }

        $roleForm->name = $role->name;
        $roleForm->description = $role->description;

        return $this->render('update', [
            'model' => $roleForm,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermission,
        ]);
    }

    /**
     * Deletes an existing Role.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return mixed
     */
    public function actionDelete($name)
    {
        $accessManager = new AccessManager();
        if (!empty(Yii::$app->authManager->getUserIdsByRole($name))) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Error delete role.'));
        } else {
            $result = Yii::$app->authManager->deleteRole($name);
            $accessManager->removeAllPermissions(new AccessRoleApplicant($name));

            if (!$result) {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Error delete role.'));
            }
        }


        return $this->redirect(['index']);
    }
}
