<?php

namespace common\models;

use Yii;
use yii\db\{ActiveRecord, ActiveQuery};
use modules\document\models\Document;

/**
 * This is the model class for table "{{%attachment_entity}}".
 *
 * @property integer $attachment_id
 * @property string $version
 * @property string $filename
 * @property integer $created_at
 *
 * @property Attachment $attachment
 */
class AttachmentEntity extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachment_entity}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attachment_id','version'], 'required'],
            [['attachment_id','created_at'], 'integer'],
            [['version'], 'string', 'max' => 14],
            [['filename'], 'string', 'max' => 64],
            [['attachment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attachment::className(), 'targetAttribute' => ['attachment_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'attachment_id' => Yii::t('app', 'Attachment ID'),
            'version' => Yii::t('app', 'Version'),
            'filename' => Yii::t('app', 'Filename'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return ActiveQuery $model
     */
    public function getAttachment()
    {
        return $this->hasOne(Attachment::className(), ['id' => 'attachment_id'])->inverseOf('attachmentEntities');
    }
}
