<?php

namespace modules\currency\controllers\backend;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\currency\models\Currency;
use modules\currency\models\CurrencySearch;
use common\controllers\BaseController;
use yii\helpers\Json;

use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for Currency model.
 */
class DefaultController extends BaseController
{



    const SUCCESSFULLY = 2;

    public function permissionMapping()
    {
        return [
            'index' => AccessManager::VIEW,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
        ];
    }

    private static function getMessages($options)
    {
        return [
            self::SUCCESSFULLY => Yii::t('app', 'Successfully adding currency'),
        ];
    }
    /**
     * Lists all Currency models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CurrencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Currency model.
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
     * Creates a new Currency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $carrencyAll = file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');
        $carrencyAll = Json::decode($carrencyAll);
        $carrencyAll = array_merge($carrencyAll, [[
            'r030' => 999999,
            'txt' => 'Украинская гривна',
            'rate' => 1,
            'cc' => 'UAH',
            'exchangedate' =>'16.03.2018'
        ]
        ]);
        foreach ($carrencyAll as $item) {
            if ($item['r030'] === 978 || $item['r030'] === 840 || $item['r030'] === 999999) {
                if ($item['r030'] === 978) {
                    $item['txt'] = 'Евро';
                }
                if ($item['r030'] === 840) {
                    $item['txt'] = 'Доллар США';
                }
                $model = new Currency();
                if($model->apiSaveCarrency($item)) {
                    $this->showMessage(self::SUCCESSFULLY, null, 'success');
                }
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing Currency model.
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
     * Deletes an existing Currency model.
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
     * Deletes existing Currency models.
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

    /**
     * Change Currency models sorting.
     * @return mixed
     */
    /*public function actionSort()
    {
        $model = new Currency;

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }*/

    /**
     * Finds the Currency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    private function showMessage($messageId, $options = [], $type = 'error')
    {
        Yii::$app->getSession()->setFlash($type, [self::getMessages($options)[$messageId]]);
    }
}
