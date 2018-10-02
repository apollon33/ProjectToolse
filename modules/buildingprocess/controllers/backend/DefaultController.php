<?php

namespace modules\buildingprocess\controllers\backend;

use modules\buildingprocess\models\Discussion;
use modules\buildingprocess\models\ProcessInstanceSearch;
use modules\document\models\Document;
use modules\field\models\ProcessFieldTemplate;
use modules\field\models\ProcessFieldTemplateSearch;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\buildingprocess\models\ProcessTemplate;
use modules\buildingprocess\models\ProcessTemplateSearch;
use common\controllers\BaseController;
use common\access\AccessManager;

/**
 * DefaultController implements the CRUD actions for BuildingProcess model.
 */
class DefaultController extends BaseController
{

    public function permissionMapping()
    {
        return [
            'index' =>  AccessManager::VIEW,
            'deal' => AccessManager::CREATE,
            'stages' => AccessManager::UPDATE,
            'sort' => AccessManager::UPDATE,
            'stages-update' => AccessManager::UPDATE,
            'stages-create' => AccessManager::CREATE,
            'stages-delete' => AccessManager::DELETE,
            'update' => AccessManager::UPDATE,
            'create' => AccessManager::CREATE,
            'delete' => AccessManager::DELETE,
            'delete-list' => AccessManager::DELETE,
        ];
    }

    /**
     * Lists all BuildingProcess models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProcessTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProcessBuilding model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDeal()
    {
        $searchModel = new ProcessInstanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('_table_deal', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $parent
     *
     * @return string
     */
    public function actionStages($parent)
    {
        $buildingProcess = new ProcessTemplate();
        $buildingProcess->loadDefaultValues();
        $buildingProcess->parent = $parent;

        return $this->renderAjax('_form_stage', [
            'path' => 'stages-create',
            'model' => $buildingProcess,
        ]);

    }

    /**
     * @return string
     */
    public function actionStagesCreate()
    {
        $buildingProcess = new ProcessTemplate();
        $buildingProcess->loadDefaultValues();
        $buildingProcess->type = "";
        if ($buildingProcess->load(Yii::$app->request->post()) && $buildingProcess->save()) {
            ProcessTemplate::sortingStageToFinal($buildingProcess->parent);
            return 'successful';
        } else {
            return $this->renderAjax('_form_stage', [
                'path' => 'stages-create',
                'model' => $buildingProcess,
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionStagesUpdate($id)
    {
        $buildingProcess = $this->findModel($id);
        if ($buildingProcess->load(Yii::$app->request->post()) && $buildingProcess->save()) {
            return 'successful';
        }
        $searcTypeField = new ProcessFieldTemplateSearch();
        $dataTypeField  = $searcTypeField->search($buildingProcess->id);
        return $this->renderAjax('_form_stage', [
            'path' => $id . '/stages-update',
            'dataTypeField' => $dataTypeField,
            'model' => $buildingProcess,
        ]);

    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionStagesDelete($id)
    {
        if (!empty($id)) {
            $this->findModel($id)->delete();

            return 'successful';
        }

        return 'error';

    }

    /**
     * Creates a new ProcessBuilding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $buildingProcess = new ProcessTemplate();
        $buildingProcess->scenario = ProcessTemplate::SCENARIO_SAVE;
        $buildingProcess->loadDefaultValues();
        $folderDir = Yii::$app->request->post('ProcessTemplate')['create_folder'];
        $allTabs = Yii::$app->request->post('ProcessTemplate')['display_all_tabs'] ? ProcessTemplate::DISPLAY_ALL_TABS_YES : ProcessTemplate::DISPLAY_ALL_TABS_NO;
        if ($buildingProcess->load(Yii::$app->request->post()) && $buildingProcess->save()) {
            if ($folderDir) {
                $modelProcessFieldTemplate = new ProcessFieldTemplate();
                $modelProcessFieldTemplate->process_id = $buildingProcess->id;
                $modelProcessFieldTemplate->type_field = ProcessFieldTemplate::DISCUSSION_FIELD;
                $modelProcessFieldTemplate->name = Discussion::NAME_OF_SELECTED_FILE;
                $modelProcessFieldTemplate->required = Discussion::REQUIRED_FIELD_VALUE;
                $modelProcessFieldTemplate->modify = ProcessFieldTemplate::REQUIRED_NO;
                $modelProcessFieldTemplate->save();
            }
            $displayAllTabsProcessFieldTemplate = new ProcessFieldTemplate();
            $displayAllTabsProcessFieldTemplate->process_id = $buildingProcess->id;
            $displayAllTabsProcessFieldTemplate->type_field = ProcessFieldTemplate::DISPLAY_ALL_TABS_FIELD;
            $displayAllTabsProcessFieldTemplate->name = ProcessFieldTemplate::DISPLAY_ALL_TABS_NAME;
            $displayAllTabsProcessFieldTemplate->required = $allTabs;
            $displayAllTabsProcessFieldTemplate->modify = ProcessFieldTemplate::REQUIRED_NO;
            $displayAllTabsProcessFieldTemplate->save();
            $clientProcessFieldTemplate = new ProcessFieldTemplate();
            $clientProcessFieldTemplate->process_id = $buildingProcess->id;
            $clientProcessFieldTemplate->type_field = ProcessFieldTemplate::CLIENT_FIELD;
            $clientProcessFieldTemplate->name = ProcessFieldTemplate::CLIENT_FIELD_NAME;
            $clientProcessFieldTemplate->required = ProcessFieldTemplate::CLIENT_CREATE_YES;
            $clientProcessFieldTemplate->modify = ProcessFieldTemplate::REQUIRED_NO;
            $clientProcessFieldTemplate->save();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $buildingProcess,
            ]);
        }
    }
    /**
     * Updates an existing ProcessBuilding model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $buildingProcess = $this->findModel($id);
        $buildingProcess->scenario = ProcessTemplate::SCENARIO_SAVE;
        $buildingProcess->changeStatusDisplayAllTabs($buildingProcess->id);
        $allTabs = Yii::$app->request->post('ProcessTemplate')['display_all_tabs'];
        if ($buildingProcess->load(Yii::$app->request->post()) && $buildingProcess->save()) {
            $buildingProcess->changeDisplayAllTabs((int)$allTabs);
            $buildingProcess->renameDocument();
            return $this->redirect(['index']);
        } else {
            $searchBuildingProcess = new ProcessTemplateSearch();
            $searcTypeField = new ProcessFieldTemplateSearch();
            $dataBuildingProcess = $searchBuildingProcess->searchStages($buildingProcess->id);
            $dataTypeField  = $searcTypeField->search($buildingProcess->id);

            return $this->render('update', [
                'model' => $buildingProcess,
                'dataProvider' => $dataBuildingProcess,
                'dataTypeField' => $dataTypeField
            ]);
        }
    }

    /**
     * Deletes an existing BuildingProcess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes existing BuildingProcess models.
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteList()
    {
        $ids = Yii::$app->request->post('ids');
        $success = false;

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $buildingProcess = $this->findModel($id);
                $success = $buildingProcess->delete();
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => $success,
        ];
    }


    /**
     * Get sorted results
     */
    public function actionSort()
    {
        $buildingProcess = new ProcessTemplate;
        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $buildingProcess->updateSorting($sortedIds) : null;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }

    /**
     * Finds the BuildingProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return ProcessTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcessTemplate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
