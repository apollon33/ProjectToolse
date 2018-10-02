<?php

use yii\db\Migration;

/**
 * Class m180205_090353_alter_column_name_in_table_attachment
 */
class m180205_090353_alter_column_name_in_table_attachment extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%attachment%}}', 'name', 'string(128)');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%attachment%}}', 'name', 'string(64)');
    }
}
