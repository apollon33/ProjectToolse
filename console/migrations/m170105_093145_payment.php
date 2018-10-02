<?php

use yii\db\Migration;

class m170105_093145_payment extends Migration
{

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'mounth' => $this->integer()->notNull(),
            'year'=> $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'amount' =>  $this->float()->notNull(),
            'tax_profit' =>  $this->float()->notNull(),
            'tax_war' =>  $this->float()->notNull(),
            'tax_pension' =>  $this->float()->notNull(),
            'payout' =>  $this->float()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_payment_user', '{{%payment}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%payment}}');
    }

}
