<?php

use yii\db\Migration;

/**
 * Class m180404_141726_resize_column_process_field
 */
class m180404_141726_resize_column_process_field extends Migration
{

    private $updateTableDocument = 'document';
    private $updateTableProcessFieldTemplate = 'process_field_template';
    private $updateTableProcessFieldInstance = 'process_field_instance';
    private $columnName = 'name';
    private $columnDate = 'data';

    public function up()
    {
        if (!in_array($this->updateTableProcessFieldTemplate, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->alterColumn("{{%$this->updateTableProcessFieldTemplate}}", $this->columnName, $this->string()->notNull());

        if (!in_array($this->updateTableProcessFieldInstance, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->alterColumn("{{%$this->updateTableProcessFieldInstance}}", $this->columnDate, $this->text()->notNull());

        if (!in_array($this->updateTableDocument, $this->getDb()->schema->tableNames)) {
            return;
        }
        $this->alterColumn("{{%$this->updateTableDocument}}", $this->columnName, $this->string()->notNull());
    }

    public function down()
    {
        return true;
    }

}
