<?php

use yii\db\Migration;

class m161115_083011_vacation_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%vacation}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'start_at' => $this->integer()->defaultValue(null),
            'end_at' => $this->integer()->defaultValue(null),
            'type' =>$this->integer()->notNull(),
            'description' => $this->text()->defaultValue(null),
        ],$tableOptions);

        $this->addForeignKey('fk_vacation_user', '{{%vacation}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');


    }

    public function down()
    {
        $this->dropTable('{{%vacation}}');
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
