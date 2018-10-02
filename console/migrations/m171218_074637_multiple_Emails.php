<?php

use yii\db\Migration;

/**
 * Class m171218_074637_multiple_Emails
 */
class m171218_074637_multiple_Emails extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%multiple_e_mails}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'email' => $this->string(100)->notNull(),
            'type' => $this->integer(1)->notNull(),
            'active' => $this->boolean()->defaultValue(true),
        ], $tableOptions);

        $this->addForeignKey('fk_email_user', '{{%multiple_e_mails}}', 'userId', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_email_user', '{{%multiple_e_mails}}');
        $this->dropTable('{{%multiple_e_mails}}');
    }
}
