<?php

namespace modules\field\models\fields;

use Yii;

use modules\field\models\Field;

use modules\document\models\Document;
use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class LinkField extends Field
{
    /**
     * @var string
     */
    public $type = 'link';


    /**
     * @return null
     */
    public function validation()
    {
        return null;
    }

    /**
     * @return array'value' => $this->value,
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
