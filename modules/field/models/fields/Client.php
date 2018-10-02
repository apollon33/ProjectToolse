<?php
namespace modules\field\models\fields;

use yii\helpers\Json;
use modules\field\models\Field;
use modules\field\models\ProcessFieldTemplate;
use modules\buildingprocess\models\ProcessTemplate;
use Yii;
use modules\field\models\ProcessFieldInstance;
use modules\buildingprocess\models\ProcessInstance;
use modules\client\models\Client as ClientModel;

class Client extends Field
{
    /**
     * @var string
     */
    public $type = 'input';

    /**
     * @return null
     */
    public function validation()
    {
        return null;
    }

    /**
     * @return array
     */
    public function option()
    {
        $process_id_template = ProcessFieldTemplate::getProcessId($this->field_id)->process_id;
        $process_template = ProcessTemplate::findOne($process_id_template);
        $client_field_id = ProcessFieldTemplate::getClientField($process_template);
        $id = Yii::$app->request->get('id');
        $data = ProcessFieldInstance::getData($id, $client_field_id->id);
        if ($data == null) {
            $test = ProcessInstance::find()->where(['id' => $id])->one();
            $result = $id - $test->parent;
            $data = ProcessFieldInstance::getData($id - $result, $client_field_id->id);
        }
        $client = ClientModel::findOne($data->data);
        $client_label = new ClientModel();
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'class' => 'form-control',
            'id' =>  str_replace(' ', '-', strtolower($this->name)),
            'value' => $client->{Json::decode($this->option)[0]} ?? Yii::t('app', 'Profile not set'),
            'labelName' => $client_label->getAttributeLabel(Json::decode($this->option)[0]),
        ];
    }
}
