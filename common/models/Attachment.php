<?php

namespace common\models;

use common\access\AccessInterface;
use Yii;
use yii\db\{ActiveRecord, ActiveQuery};
use common\behaviors\AccessBehavior;
use modules\document\models\Document;
use yii\db\Query;

/**
 * This is the model class for table "{{%attachment}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property AttachmentEntity[] $attachmentEntities
 * @property Document $document
 */
class Attachment extends ActiveRecord implements AccessInterface
{

    const ATTACHMENT_FILE_TYPE = 'image';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => AccessBehavior::className(),
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUPrefix() : string
    {
        return 'img-';
    }

    /**
     * @inheritdoc
     */
    public function getUId() : string
    {
        return $this->getUPrefix() . $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getUType()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isUOwner()
    {
        return false;
    }

    /**
     * return owner of this document
     * @return AccessInterface
     */
    public function getUOwner()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUParent()
    {
        return Attachment::findOne($this->origin_id);
    }

    /**
     * @return ActiveQuery $models
     */
    public function getAttachmentEntities()
    {
        return $this->hasMany(AttachmentEntity::className(), ['attachment_id' => 'id'])->inverseOf('attachment');
    }
    
    /**
     * @return string
     */
    public function getFullPath()
    {
        $query = new Query();
        $parent = $query->select('document_id')->from('attachment_document')->where(['attachment_id' => $this->id])->one();

        return 'document/' . $parent['document_id'] . '/attachment?imageKey=' .$this->img_name;
    }
    /**
     * @return ActiveQuery $model
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id'])->viaTable('attachment_document', ['attachment_id' => 'id']);
    }
    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['origin_id']);

        return array_merge($fields, [
            'url' => 'fullPath',
        ]);
    }
    
    /**
     * @param $entityId
     * @param $filename
     * @return string
     */
    public function getDownloadUrl(int $entityId,  string $filename ): string
    {
        $route = Yii::$app->urlManager->parseRequest(Yii::$app->request);
        if ($route === false) {
            return '#';
        }
        $path = explode('/', $route[0]);
        array_splice($path, -1, 1, 'attachment');
         return Yii::$app->urlManager->createUrl([implode('/', $path), 'id' => $entityId, 'imageKey' => $filename]);
    }

    /**
     * @return AttachmentEntity
     */
    public function getLastVersion(): AttachmentEntity
    {
        $versions = $this->attachmentEntities;
        return end($versions);
    }
}
