<?php

use yii\db\Migration;

class m161115_082901_holiday_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%holiday}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'date' => $this->integer()->defaultValue(null),
        ],$tableOptions);

        $this->createTable('{{%holiday_config}}', [
            'id' => $this->primaryKey(),
            'month' => $this->integer()->defaultValue(null),
            'day' => $this->integer()->defaultValue(null),
            'name' => $this->string()->notNull(),
        ],$tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%holiday_config}}');
        $this->dropTable('{{%holiday}}');
    }
}
