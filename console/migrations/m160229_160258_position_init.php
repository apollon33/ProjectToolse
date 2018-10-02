<?php

use yii\db\Migration;

class m160229_160258_position_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%position}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
        ], $tableOptions);

        $this->batchInsert('{{%position}}', ['id', 'name'], [
            [1, 'Кандидат-программист'],
            [2, 'Кандидат-менеджер'],
            [3, 'Ученик'],
            [4, 'Стажер-программист'],
            [5, 'Программист'],
            [6, 'Стажер-менеджер'],
            [7, 'Менеджер'],
            [8, 'Старший менеджер'],
            [9, 'Бухгалтер'],
            [10, 'Завхоз'],
            [11, 'Вахтер'],
            [12, 'Уборщица'],
        ]);
        
        $this->addForeignKey('fk_user_position', '{{%user}}', 'position_id', '{{%position}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_position', '{{%user}}');

        $this->dropTable('{{%position}}');
    }
}
