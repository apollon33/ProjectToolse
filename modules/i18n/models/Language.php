<?php

namespace modules\i18n\models;

use Yii;
use yii\db\ActiveRecord;
use common\behaviors\ReadOnlyBehavior;

/**
 * This is the model class for table "{{%language}}".
 *
 * @property integer $id
 * @property string $language
 * @property string $name
 *
 * @property Translation[] $translations
 * @property Message[] $ids
 */
class Language extends ActiveRecord
{
    const DEFAULT_LANGUAGE = 'en';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%i18n_language}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ReadOnlyBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language', 'name'], 'required'],
            [['language'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 50],
            [['language'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'language' => Yii::t('app', 'Language'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(Translation::className(), ['language' => 'language']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIds()
    {
        return $this->hasMany(Message::className(), ['id' => 'id'])->viaTable('{{%translation}}', ['language' => 'language']);
    }

    /**
     * @param boolean $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $ids = Message::getIds();
            $rows = [];
            foreach ($ids as $id) {
                $rows[] = [$id, $this->language];
            }

            Yii::$app->db->createCommand()->batchInsert(Translation::tableName(), ['id', 'language'], $rows)->execute();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param bool $withDefault
     * @param null $visible
     * @return array
     */
    public static function getList($withDefault = true, $visible = null)
    {
        $where = [];
        if (!$withDefault) {
            $where = ['!=', 'language', self::DEFAULT_LANGUAGE];
        }

        return self::find()->where($where)->select(['name', 'language'])->indexBy('language')->column();
    }

    /**
     * @return array
     */
    public static function getIdList()
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}
