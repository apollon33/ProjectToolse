<?php

namespace modules\logregistration\controllers\backend;

use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Json;
use modules\logregistration\models\LogRegistration;
use common\models\User;
use common\controllers\BaseController;

use common\access\AccessManager;


class DefaultController extends BaseController
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
     * @return string
     */
    public function actionCreate()
    {
        $model = new LogRegistration();
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = $this->findModel($model->user_id);
            $user->scenario = User::SCENARIO_SAVE_USER_POSITION;
            $user->registration_id = $model->registration_id;
            $user->save();
            if(Yii::$app->request->isAjax) {
                return 'complete';
            }
        } else {
            if(Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = LogRegistration::find()->where(['user_id'=>$id])->orderBy(['id' => SORT_DESC])->one();
        (!empty($model)? $model: $model = new LogRegistration());
        if(Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'user_id' => $id,
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionDelete($id)
    {
        $this->findModelLogRegistrarion($id)->delete();
        return 'complete';
    }

    /**
     * @param $id
     *
     * @return LogRegistration
     * @throws NotFoundHttpException
     */
    protected function findModelLogRegistrarion($id)
    {
        if (($model = LogRegistration::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * @param $id
     *
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }




}
