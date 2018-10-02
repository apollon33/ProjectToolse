<?php

use yii\db\Migration;

/**
 * Class m180321_072905_change_process_field_instance_table
 */
class m180321_072905_change_process_field_instance_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%process_field_instance}}', 'data', 'text');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%process_field_instance', 'data', 'varchar(100)');
    }

}
