<?php

namespace modules\field\models\fields;

use modules\field\models\Field;

class DisplayAllTabsField extends Field
{
    public $type = 'checkbox';

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
            'class' => 'field-name form-control js-displayAllTabs-input',
            'id' => $this->name,
            'value' => 0,
            'hidden' => true,
            'labelName' => $this->name
        ];
    }

}

