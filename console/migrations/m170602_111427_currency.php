<?php

use yii\db\Migration;

class m170602_111427_currency extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey(),
            'key_digital' => $this->integer()->notNull(),
            'key_letter' => $this->string(10)->notNull(),
            'date'=>$this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'rate' => $this->float()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%currency}}');
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
