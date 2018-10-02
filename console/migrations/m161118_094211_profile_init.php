<?php

use yii\db\Migration;

class m161118_094211_profile_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'first_name' => $this->string(100)->notNull(),
            'last_name' => $this->string(100)->notNull(),
            'rate' => $this->float()->defaultValue(null),
            'login' => $this->string(100)->defaultValue(null),
            'password' => $this->string()->defaultValue(null),
            'email' => $this->string()->defaultValue(null),
            'email_password' => $this->string()->defaultValue(null),
            'skype' => $this->string()->defaultValue(null),
            'skype_password' => $this->string()->defaultValue(null),
            'description' => $this->string(255)->defaultValue(null),
            'verification' => $this->string(255)->defaultValue(null),
            'note' => $this->text()->defaultValue(null),
        ], $tableOptions);


    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
    }

}
