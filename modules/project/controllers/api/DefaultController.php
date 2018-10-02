<?php
namespace modules\project\controllers\api;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class DefaultController extends Controller
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        return [
            'status' => true,
            'projects' => Yii::$app->user->identity->projects
        ];
        /*return new ActiveDataProvider([
            'query' => Yii::$app->user->identity->getProjects(),
        ]);*/
    }

}