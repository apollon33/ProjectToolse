<?php

use yii\db\Migration;

/**
 * Class m180129_115841_change_attachment_table
 */
class m180129_115841_change_attachment_table extends Migration
{
    private $updateTableUser = 'attachment';
    private $addColumn = 'name';
    private $dropColumnImg = 'img_name';
    private $dropColumnOrigin = 'origin_id';
    private $dropColumnUrl = 'url';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            "{{%attachment}}",
            $this->addColumn,
            $this->string(64)->defaultValue(null)
        );
        $this->dropColumn(
            "{{%attachment}}",
            $this->dropColumnImg
        );
        $this->dropColumn(
            "{{%attachment}}",
            $this->dropColumnOrigin
        );
        $this->dropColumn(
            "{{%attachment}}",
            $this->dropColumnUrl
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(
            "{{%attachment}}",
            $this->addColumn
        );
        $this->addColumn(
            "{{%attachment}}",
            $this->dropColumnImg,
            $this->string(64)->defaultValue(null)
        );
        $this->addColumn(
            "{{%attachment}}",
            $this->dropColumnOrigin,
            $this->string()->defaultValue(null)
        );
        $this->addColumn(
            "{{%attachment}}",
            $this->dropColumnUrl,
            $this->string(64)
        );
    }
}
