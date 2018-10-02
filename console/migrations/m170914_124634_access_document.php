<?php

use yii\db\Migration;

class m170914_124634_access_document extends Migration
{
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%access_document}}', [
            'instance_id' => $this->string(64),
            'access_type' => "ENUM('1', '2')",
            'access_id' => $this->string(64),
            'permission' => $this->integer()->defaultValue(0),
            'PRIMARY KEY(instance_id, access_type, access_id)',
        ],$tableOptions);

    }

    public function safeDown()
    {

        $this->dropTable('{{%access_document}}');

    }
}
