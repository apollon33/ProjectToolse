<?php
namespace modules\field\models\fields;

use modules\field\models\Field;

/**
 * Class ClientField
 * @package modules\field\models\fields
 */
class ClientField extends Field
{
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
            'class' => 'field-name form-control js-client-input',
            'id' => $this->name,
            'value' => 0,
            'hidden' => true,
            'labelName' => $this->name
        ];
    }
}


