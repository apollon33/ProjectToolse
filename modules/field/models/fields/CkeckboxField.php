<?php

namespace modules\field\models\fields;

use Yii;
use yii\helpers\Json;

use modules\field\models\Field;

use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class CkeckboxField extends Field
{
    /**
     * @var string
     */
    public $type = 'input';

    /**
     * @return array
     */
    public function validation()
    {
        return [
            'type' => 'number',
            'messages' => [
                'pattern' => '/^\s*[+-]?\d+\s*$/',
                'message' => Yii::t('app', $this->name . ' must be an integer.'),
                'skipOnEmpty' => 1,
            ],
        ];
    }

    /**
     * @param $value
     * @param ProcessInstance $processInstances
     * @param ProcessFieldTemplate $processFieldTemplate
     *
     * @return array
     */
    public function load($value, ProcessInstance $processInstances, ProcessFieldTemplate $processFieldTemplate)
    {
        $value = Json::encode($value);
        return parent::load($value, $processInstances, $processFieldTemplate);
    }


    /**
     * @return array
     */
    public function option()
    {
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'type' => 'checkbox',
            'class' => 'form-control',
            'id' =>  str_replace(' ', '-', strtolower($this->name)),
            'option' => Json::decode($this->option),
            'value' => Json::decode($this->value),
            'labelName' => $this->name,
        ];
    }


}

