<?php

use yii\db\Migration;

class m160229_160260_registration_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%registration}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
        ], $tableOptions);

        $this->batchInsert('{{%registration}}', ['id', 'name'], [
            [1, 'ГПХ'],
            [2, 'ФОП'],
            [3, 'Трудовая'],
        ]);
        
        $this->addForeignKey('fk_user_registration', '{{%user}}', 'registration_id', '{{%registration}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_registration', '{{%user}}');

        $this->dropTable('{{%registration}}');
    }
}
