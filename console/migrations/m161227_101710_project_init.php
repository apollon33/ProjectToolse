<?php

use yii\db\Migration;

class m161227_101710_project_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'client_id' => $this->integer()->notNull(),
            'profile_id' => $this->integer()->defaultValue(null),
            'color' =>$this->string(7)->defaultValue(null),
            'start_at' => $this->integer()->defaultValue(null),
            'end_at' => $this->integer()->defaultValue(null),
            'access' => $this->integer(1)->notNull(),
            'rate' => $this->float()->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
        ], $tableOptions);
        $this->addForeignKey('fk_project_client', '{{%project}}', 'client_id', '{{%client}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_project_profile', '{{%project}}', 'profile_id', '{{%profile}}', 'id', 'RESTRICT', 'CASCADE');

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_project_client', '{{%project}}');
        $this->dropForeignKey('fk_project_profile', '{{%project}}');
        $this->dropTable('{{%project}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
