<?php

use yii\db\Migration;

/**
 * Class m180122_140004_add_column_document_type_to_document
 */
class m180122_140004_add_column_document_type_to_document extends Migration
{
    private $updateTable = 'document';
    private $addColumn = 'document_type';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!in_array($this->updateTable, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%document}}");
        if (empty($table->columns[$this->addColumn])) {
            $this->addColumn(
                "{{%document}}",
                $this->addColumn,
                $this->integer()->defaultValue(1)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if (!in_array($this->updateTable, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%document}}");
        if (!empty($table->columns[$this->addColumn])) {
            $this->dropColumn(
                "{{%document}}",
                $this->addColumn
            );
        }
    }
}
