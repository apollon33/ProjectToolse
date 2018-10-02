<?php

namespace common\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

use common\access\PathChain;

/**
 * UserController implements the CRUD actions for User model.
 */
class BaseController extends Controller
{
    const HTTP_STATUS_UNPROCESSABLE_ENTITY = 422;

    public $uId;

    /**
     * @return array
     */
    public function behaviors()
    {
        $action = $this->action->id;
        $permissionMap = $this->permissionMapping();
        $permission = array_key_exists($action, $permissionMap) ? $permissionMap[$action] : 0;

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'actions' => [$action],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [$action],
                        'roles' => [$permission],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array of permissions for module
     */
    public function permissionMapping()
    {
        return [];
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $language = Yii::$app->session->get('language', 'en');
        Yii::$app->language = $language;

        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function uid()
    {
        return $this->uId ?: $this->id;
    }

}
