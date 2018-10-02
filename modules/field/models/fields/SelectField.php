<?php

namespace modules\field\models\fields;

use yii\helpers\Json;

use modules\field\models\Field;

use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class SelectField extends Field
{
    /**
     * @var string
     */
    public $type = 'select';

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
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'class' => 'form-control',
            'id' =>  str_replace(' ', '-', strtolower($this->name)),
            'option' => array_combine(Json::decode($this->option), Json::decode($this->option)),
            'value' => $this->value,
            'labelName' => $this->name,
        ];
    }

}
