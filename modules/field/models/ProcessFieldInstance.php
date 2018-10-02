<?php

namespace modules\field\models;

use modules\document\models\Document;
use Yii;
use yii\db\ActiveRecord;

use modules\buildingprocess\models\ProcessInstance;

/**
 * This is the model class for table "{{%process_field_instance}}".
 *
 * @property integer $instance_id
 * @property integer $field_id
 * @property string $data
 *
 * @property ProcessInstance $instance
 * @property ProcessFieldTemplate $field
 */
class ProcessFieldInstance extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%process_field_instance}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instance_id', 'field_id'], 'required'],
            [['instance_id', 'field_id'], 'integer'],
            [['data'], 'string'],
            [['instance_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcessInstance::className(), 'targetAttribute' => ['instance_id' => 'id']],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcessFieldTemplate::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instance_id' => Yii::t('app', 'Instance ID'),
            'field_id' => Yii::t('app', 'Field ID'),
            'data' => Yii::t('app', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstance()
    {
        return $this->hasOne(ProcessInstance::className(), ['id' => 'instance_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(ProcessFieldTemplate::className(), ['id' => 'field_id']);
    }

    /**
     * @param $instance_id
     * @param $field_id
     */
    public static function fieldValue($instance_id, $field_id, $value)
    {
        return self::find()->where(['instance_id' => $instance_id, 'field_id' => $field_id, 'data' => $value])->one()->data;
    }

    /**
     * @param ProcessInstance $processInstance
     * @param ProcessFieldTemplate $processFieldTemplate
     */
    public function deleteDocumentFieldLink(ProcessInstance $processInstance, ProcessFieldTemplate $processFieldTemplate)
    {
        $oneFieldLink = ProcessFieldInstance::find()->where(['instance_id' => $processInstance->id, 'field_id' => $processFieldTemplate->id])->one();
        if (!empty($oneFieldLink)) {
            Document::deleteDocument(intval($oneFieldLink->data));
        }
    }

    /**
     * @param integer $instance_id
     * @param integer $field_id
     * @return array|ProcessFieldInstance|null|ActiveRecord
     */
    public static function getData($instance_id, $field_id)
    {
        return self::find()->where(['instance_id' => $instance_id, 'field_id' => $field_id])->one();
    }
}
