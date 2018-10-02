<?php

namespace api\controllers;

use yii\web\Controller;
use api\models\forms\LoginApi;
use Yii;

class LoginController extends Controller
{
    public function actionIndex()
    {
        $model = new LoginApi();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->getBodyParams(), '')) {

            if ($user = $model->auth()) {

                return [
                    'access_token' => $user->access_token,
                    'user' => $user,
                ];

            } else {
                return [
                    'errors' => $model->errors,
                ];
            }
        } else {
            return [
                'errors' => 'Data not transferred',
            ];
        }
    }
}