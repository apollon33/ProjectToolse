<?php

namespace modules\field\models\fields;

use Yii;

use modules\field\models\Field;

use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class TextField extends Field
{
    /**
     * @var string
     */
    public $type = 'textarea';

    /**
     * @return array
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
            'value' => $this->value,
            'labelName' => $this->name,
        ];
    }


}
