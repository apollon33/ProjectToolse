<?php

use yii\db\Migration;

/**
 * Class m180102_140958_attachment
 */
class m180102_140958_attachment extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%attachment}}', [
            'id' => $this->primaryKey(),
            'img_name' => $this->string(64),
            'url' => $this->string(64),
        ], $tableOptions);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%attachment}}');

    }

}
