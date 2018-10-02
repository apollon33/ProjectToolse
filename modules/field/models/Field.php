<?php

namespace modules\field\models;

use Yii;

use modules\buildingprocess\models\ProcessInstance;

abstract class Field
{
    public $attachmentType = false;
    /**
     * @var int
     */
    public $field_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $required;

    /**
     * @var boolean
     */
    public $modify;

    /**
     * @var string
     */
    public $option;

    /**
     * @var string
     */
    public $value = null;

    /**
     * Field constructor.
     * @param ProcessFieldTemplate $fieldTemplate
     * @param ProcessInstance $processInstance
     */
    public function __construct(ProcessFieldTemplate $fieldTemplate, $processInstance)
    {
        $this->field_id = $fieldTemplate->id;
        $this->name = $fieldTemplate->name;
        $this->required = $fieldTemplate->required;
        $this->modify = $fieldTemplate->modify;
        $this->option = $fieldTemplate->option;
        if (!empty($processInstance) && !empty($processInstance->findModelProcessFieldInstance($fieldTemplate))) {
            $this->value = $processInstance->findModelProcessFieldInstance($fieldTemplate)->data;
        }
    }

    /**
     *
     */
    public function view()
    {

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
        $res = false;
        return [
            'ProcessFieldInstance' => [
                'instance_id' => $processInstances->id,
                'field_id' => $processFieldTemplate->id,
                'data' => $value,
            ],
        ];
    }

    /**
     * @return array
     */
    public function requiredValidation()
    {
        $message = Yii::t('app', $this->name . ' cannot be blank.');

        return [
            'type' => 'required',
            'messages' => [
                'message' => $message,
            ],
        ];
    }

}
