<?php

use yii\db\Migration;

class m161227_121622_calendar_init extends Migration
{
    /*
    // Use safeUp/safeDown to run migration code within a transaction*/
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%calendar}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(null),
            'project_id' => $this->integer()->notNull(),
            'estimated_time' => $this->integer()->defaultValue(null),
            'estimate_approval' => $this->boolean()->notNull()->defaultValue(false),
            'actual_time' => $this->integer()->defaultValue(null),
            'start_at' => $this->integer()->defaultValue(null),
            'end_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
        ],$tableOptions);

        $this->addForeignKey('fk_calendar_user', '{{%calendar}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_calendar_created_by', '{{%calendar}}', 'created_by', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_calendar_project', '{{%calendar}}', 'project_id', '{{%project}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('calendar');
    }
}
