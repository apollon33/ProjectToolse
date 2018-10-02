<?php

use yii\db\Migration;

class m161227_114608_user_project_init extends Migration
{
    /*
    Use safeUp/safeDown to run migration code within a transaction*/
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_project}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(null),
            'project_id' => $this->integer()->defaultValue(null),
        ], $tableOptions);
        $this->addForeignKey('fk_user_user_project', '{{%user_project}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_project_user_project', '{{%user_project}}', 'project_id', '{{%project}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('user_project');
    }

}
