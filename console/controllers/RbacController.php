<?php

namespace console\controllers;

use common\access\AccessManager;
use common\access\AccessChainsDependencies;
use common\access\AccessRoleApplicant;
use Yii;
use yii\console\Controller;
use common\models\User;
use yii\helpers\ArrayHelper;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $accessManager = new AccessManager();

        $auth->removeAll();

        $user = $auth->createRole('user');
        $auth->add($user);

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $employee = $auth->createRole('employee');
        $auth->add($employee);

        $project_manager = $auth->createRole('project-manager');
        $auth->add($project_manager);

        $sales_manager = $auth->createRole('sales-manager');
        $auth->add($sales_manager);

        $superAdmin = $auth->createRole('super-admin');
        $auth->add($superAdmin);

        $coreUser = $auth->createPermission('core.backend.user');
        $auth->add($coreUser);

        $coreAdmin = $auth->createPermission('core.backend.admin');
        $auth->add($coreAdmin);

        $coreRole = $auth->createPermission('core.backend.role');
        $auth->add($coreRole);

        $auth->assign($superAdmin, User::DEFAULT_ADMIN_ID);

        $accessManager->assignPermissions(
            new AccessChainsDependencies('core'),
            new AccessRoleApplicant($user->name),
            AccessManager::ASSIGN
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core.backend.user'),
            new AccessRoleApplicant($user->name),
            AccessManager::UPDATE
        );

        $accessManager->assignPermissions(
            new AccessChainsDependencies('core'),
            new AccessRoleApplicant($admin->name),
            AccessManager::ASSIGN
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core'),
            new AccessRoleApplicant($admin->name),
            AccessManager::UPDATE
        );

        $accessManager->assignPermissions(
            new AccessChainsDependencies('core'),
            new AccessRoleApplicant($employee->name),
            AccessManager::ASSIGN
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core.backend.user'),
            new AccessRoleApplicant($employee->name),
            AccessManager::UPDATE
        );

        $accessManager->assignPermissions(
            new AccessChainsDependencies('core'),
            new AccessRoleApplicant($project_manager->name),
            AccessManager::ASSIGN
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core.backend.user'),
            new AccessRoleApplicant($project_manager->name),
            AccessManager::UPDATE
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core.backend.admin'),
            new AccessRoleApplicant($project_manager->name),
            AccessManager::VIEW
        );

        $accessManager->assignPermissions(
            new AccessChainsDependencies('core'),
            new AccessRoleApplicant($sales_manager->name),
            AccessManager::ASSIGN
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core.backend.user'),
            new AccessRoleApplicant($sales_manager->name),
            AccessManager::UPDATE
        );
        $accessManager->assignPermissions(
            new AccessChainsDependencies('core.backend.admin'),
            new AccessRoleApplicant($sales_manager->name),
            AccessManager::VIEW
        );

        /**
         * Assign role by users. Default role=user
         */
        $role_user = $auth->getRole('user');

        $users = User::find()
                     ->select('id')
                     ->where('id != ' . User::DEFAULT_ADMIN_ID)
                     ->asArray()
                     ->all();
        $ids = ArrayHelper::getColumn($users, 'id');
        foreach ($ids as $id) {
            $auth->assign($role_user, $id);
        }
    }
}
