<?php

namespace modules\holidayconfig\controllers\backend;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\holidayconfig\models\HolidayConfig;
use modules\holidayconfig\models\HolidayConfigSearch;
use modules\holiday\models\Holiday;
use common\controllers\BaseController;

use common\access\AccessManager;

/**
 * DefauldController implements the CRUD actions for HolidayConfig model.
 */
class DefaultController extends BaseController
{


    const VERY_COPY_CONFIGURATION = 2;
    const NO_DATA_COPY_CONFIGURATION = 3;

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
            'putdown' => AccessManager::CREATE,
        ];
    }

    private static function getMessages($options)
    {
        return [
            self::VERY_COPY_CONFIGURATION => Yii::t('app', 'Very good copy configuration'),
            self::NO_DATA_COPY_CONFIGURATION => Yii::t('app', 'Not data copy configuration'),
        ];
    }
    /**
     * Lists all HolidayConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HolidayConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HolidayConfig model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new HolidayConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HolidayConfig();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HolidayConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Deletes an existing HolidayConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes existing HolidayConfig models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteList()
    {
        $ids = Yii::$app->request->post('ids');
        $success = false;

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->findModel($id);
                $success = $model->delete();
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $success,
        ];
    }


    public function actionPutdown()
    {
        $holiday = new Holiday();
        if($holiday->copyConfiguration()) {
            $this->showMessage(self::VERY_COPY_CONFIGURATION, null, 'success');
        } else {
            $this->showMessage(self::NO_DATA_COPY_CONFIGURATION, null, 'warning');
        }
        return $this->redirect(['index']);

    }

    private function showMessage($messageId, $options = [], $type = 'error')
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages($options)[$messageId]]);
    }




    /**
     * Finds the HolidayConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HolidayConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HolidayConfig::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
