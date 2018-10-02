<?php

namespace modules\logposition\controllers\backend;

use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Json;
use modules\logposition\models\LogPosition;
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
        $model = new LogPosition();
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = $this->findModel($model->user_id);
            $user->scenario = User::SCENARIO_SAVE_USER_POSITION;
            $user->position_id = $model->position_id;
            $user->save();
            if(Yii::$app->request->isPjax) {
                return 'complete';
            }
        } else {
            if(Yii::$app->request->isPjax) {
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
        $model = LogPosition::find()->where(['user_id'=>$id])->orderBy(['id' => SORT_DESC])->one();
        (!empty($model)? $model: $model = new LogPosition());
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
        $this->findModelLogPosition($id)->delete();

        return 'complete';
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

    /**
     * @param $id
     *
     * @return LogPosition
     * @throws NotFoundHttpException
     */
    protected function findModelLogPosition($id)
    {
        if (($model = LogPosition::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }




}
