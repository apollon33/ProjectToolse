<?php

use yii\db\Migration;

class m170428_144534_activity extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%activity}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'project_id' => $this->integer()->defaultValue(null),
            'screenshot' => $this->string(255)->defaultValue(null),
            'target_window' => $this->string(100)->defaultValue(null),
            'keyboard_activity_percent' => $this->integer()->defaultValue(null),
            'mouse_activity_percent' => $this->integer()->defaultValue(null),
            'interval' => $this->integer()->notNull(),
            'description' => $this->string(100)->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),

        ], $tableOptions);

        $this->addForeignKey('fk_activity_user', '{{%activity}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_activity_project', '{{%activity}}', 'project_id', '{{%project}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_activity_user', '{{%activity}}');
        $this->dropForeignKey('fk_activity_project', '{{%activity}}');
        $this->dropTable('activity');
    }

}
