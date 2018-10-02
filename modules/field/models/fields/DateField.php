<?php

namespace modules\field\models\fields;

use Yii;
use yii\helpers\Json;

use modules\field\models\Field;

use modules\buildingprocess\models\ProcessInstance;
use modules\field\models\ProcessFieldTemplate;

class DateField extends Field
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
                'pattern' => '(?:(?:0[1-9]|1[0-2])[\/\\-. ]?(?:0[1-9]|[12][0-9])|(?:(?:0[13-9]|1[0-2])[\/\\-. ]?30)|(?:(?:0[13578]|1[02])[\/\\-. ]?31))[\/\\-. ]?(?:19|20)[0-9]{2}',
                'message' => Yii::t('app', $this->name . ' must be an date.'),
                'skipOnEmpty' => 1
            ]
        ];
    }

    /**
     * @return array
     */
    public function option()
    {
        return [
            'name' => 'Field[' . $this->field_id . ']',
            'class' => 'form-control date',
            'id' =>  str_replace(' ', '-', strtolower($this->name)),
            'value' => $this->value,
            'labelName' => $this->name,
        ];
    }

}
