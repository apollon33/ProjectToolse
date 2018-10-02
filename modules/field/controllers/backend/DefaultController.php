<?php

namespace modules\field\controllers\backend;

use modules\client\models\Client;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\field\models\ProcessFieldTemplate;
use modules\field\models\ProcessFieldTemplateSearch;
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
            'field'        =>  'buildingprocess.backend.default:' . AccessManager::CREATE,
            'field-update' =>  'buildingprocess.backend.default:' . AccessManager::UPDATE,
            'field-create' =>  'buildingprocess.backend.default:' . AccessManager::CREATE,
            'field-delete' =>  'buildingprocess.backend.default:' . AccessManager::DELETE,
            'option-field' =>  'buildingprocess.backend.default:' . AccessManager::UPDATE,
            'sort'         =>  'buildingprocess.backend.default:' . AccessManager::UPDATE,
            'update'       =>  'buildingprocess.backend.default:' . AccessManager::UPDATE,
        ];
    }

    /**
     * @param $parent
     *
     * @return string
     */
    public function actionField($process_id)
    {
        $typeField = new ProcessFieldTemplate();
        $typeField->loadDefaultValues();

        $typeField->process_id = $process_id;
        $client = new Client();
        return $this->renderAjax('_form', [
            'path'  => 'field-create',
            'model' => $typeField,
            'client_field' => array_merge([''=> 'Select...'], $client->fieldAttributeLabels()),
            'selectTableFiled' => ProcessFieldTemplate::getSelectableFieldTypes()
        ]);

    }

    /**
     * @param $parent
     *
     * @return string
     */
    public function actionFieldStage($process_id)
    {
        $typeField = new ProcessFieldTemplate();
        $typeField->loadDefaultValues();

        $typeField->process_id = $process_id;
        $client = new Client();
        return $this->renderAjax('_form', [
            'path'  => 'field-create',
            'model' => $typeField,
            'client_field' => array_merge([''=> 'Select...'], $client->fieldAttributeLabels()),
            'selectTableFiled' => ProcessFieldTemplate::selectableFieldTypes()
        ]);

    }



    /**
     * @return string
     */
    public function actionFieldCreate()
    {
        $typeField = new ProcessFieldTemplate();
        $typeField->loadDefaultValues();
        if ($typeField->load(Yii::$app->request->post()) && $typeField->save()) {
            return 'success';
        } else {
            return $this->renderAjax('_form', [
                'path'  => 'field-create',
                'model' => $typeField,
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionFieldUpdate($id)
    {
        $typeField = $this->findModel($id);
        $client = new Client();
        if ($typeField->load(Yii::$app->request->post()) && $typeField->save()) {
            return 'success';
        } else {
            return $this->renderAjax('_form', [
                'path'  => $id . '/field-update',
                'model' => $typeField,
                'client_field' => $client->fieldAttributeLabels(),

            ]);
        }

    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionFieldDelete($id)
    {
        $this->findModel($id)->delete();

        return 'success';
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function actionOptionField($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (in_array($id, [ProcessFieldTemplate::CKECKBOX_FIELD, ProcessFieldTemplate::SELECT_FIELD])) {
            return true;
        } elseif (in_array($id, [ProcessFieldTemplate::CLIENT])) {
            return 'client';
        }

        return false;
    }

    /**
     * Get sorted results
     */
    public function actionSort()
    {
        $model = new ProcessFieldTemplate();

        $sortedIds = Yii::$app->request->post('sortedList');
        $success = !empty($sortedIds) ? $model->updateSorting($sortedIds) : null;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success];
    }

    /**
     * Finds the BuildingProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return TypeField the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcessFieldTemplate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
