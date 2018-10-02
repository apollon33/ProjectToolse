<?php
namespace modules\site\controllers\api;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * @return array
     */
    public function actionError()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => false];
    }
}