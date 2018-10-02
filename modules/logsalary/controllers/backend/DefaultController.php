<?php

namespace modules\logsalary\controllers\backend;

use Yii;
use yii\web\NotFoundHttpException;
use modules\logsalary\models\LogSalary;
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
        $model = new LogSalary();
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = $this->findModel($model->user_id);
            $user->scenario = User::SCENARIO_SAVE_USER_POSITION;
            $user->salary = $model->salary;
            $user->reporting_salary = $model->reporting_salary;
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
        $model = LogSalary::find()->where(['user_id'=>$id])->orderBy(['id' => SORT_DESC])->one();
        (!empty($model)? $model: $model = new LogSalary());
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
        $this->findModelLogSalary($id)->delete();

        return 'complete';
    }

    /**
     * @param $id
     *
     * @return LogSalary
     * @throws NotFoundHttpException
     */
    protected function findModelLogSalary($id)
    {
        if (($model = LogSalary::findOne($id)) !== null) {
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
