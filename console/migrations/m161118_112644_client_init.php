<?php

use yii\db\Migration;

class m161118_112644_client_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(100)->notNull(),
            'last_name' => $this->string(100)->notNull(),
            'email' => $this->string()->defaultValue(null),
            'skype' => $this->string()->defaultValue(null),
            'phone' => $this->string()->defaultValue(null),
            'country_id' => $this->integer()->defaultValue(null),
            'address' => $this->string(100)->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
            'deleted' => $this->integer(1)->notNull(),
        ], $tableOptions);


        $this->addForeignKey('fk_user_client', '{{%client}}', 'country_id', '{{%country}}', 'id', 'SET NULL', 'CASCADE');

    }

    public function down()
    {
        $this->dropForeignKey('fk_user_client', '{{%client}}');
        $this->dropTable('{{%client}}');
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
