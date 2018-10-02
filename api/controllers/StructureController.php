<?php

namespace api\controllers;

use modules\department\models\Department;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

use common\access\AccessManager;

class StructureController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['permission'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['core.backend.user:' . AccessManager::VIEW],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {

        $departments = Department::find()->all();
        return $departments;
    }

    /**
     * Displays permissions on actions with structures
     * @return array
     */
    public function actionPermission()
    {
        return [
            'GET /structure' => \Yii::$app->user->can('core.backend.user:' . AccessManager::VIEW),
        ];
    }
}