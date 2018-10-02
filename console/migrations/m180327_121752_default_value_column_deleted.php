<?php

use yii\db\Migration;

/**
 * Class m180327_121752_default_value_column_deleted
 */
class m180327_121752_default_value_column_deleted extends Migration
{
    private $updateTableUser = 'user';
    private $deletedColumn = 'deleted';

    public function up()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->alterColumn("{{%$this->updateTableUser}}", $this->deletedColumn, $this->integer(1)->defaultValue(0));
    }

    public function down()
    {
        return true;
    }
}
