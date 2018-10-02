<?php

namespace modules\field\models\fields;

use modules\field\models\Field;

use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class TitleField extends Field
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
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'class' => 'field-name form-control',
            'id' => str_replace(' ', '-', strtolower($this->name)),
            'value' => $this->value,
            'labelName' => $this->name,
        ];
    }

}
