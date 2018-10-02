<?php
namespace modules\activity\controllers\api;

use modules\activity\models\Activity;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class DefaultController extends Controller
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

    /**
     * Creates a new Activity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Activity();
        $model->loadDefaultValues();

        if ($model->load([$model->formName() => Yii::$app->request->post()])) {
            $model->user_id = Yii::$app->user->id;

            if ($model->save()) {
                return [
                    'status' => true,
                    'result' => [
                        'id' => $model->id,
                    ]
                ];
            }
        }
        return [
            'status' => false,
            'errors' => $model->errors,
        ];
    }

}