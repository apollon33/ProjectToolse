<?php

use yii\db\Migration;

/**
 * Class m180403_084019_add_field_process_field_template
 */
class m180403_084019_add_field_process_field_template extends Migration
{
    private $updateTableProcessInstance = 'process_field_template';
    private $columnModify = 'modify';

    public function up()
    {
        if (!in_array($this->updateTableProcessInstance, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->addColumn(
            "{{%$this->updateTableProcessInstance}}",
            $this->columnModify,
            $this->boolean()->notNull()->defaultValue(false)
        );
    }

    public function down()
    {
        return true;
    }
}
