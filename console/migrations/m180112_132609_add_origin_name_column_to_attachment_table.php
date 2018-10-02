<?php

use yii\db\Migration;

/**
 * Handles adding origin_name to table `attachment`.
 */
class m180112_132609_add_origin_name_column_to_attachment_table extends Migration
{
    private $updateTableUser = 'attachment';
    private $addColumn = 'origin_id';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%attachment}}");
        if (empty($table->columns[$this->addColumn])) {
            $this->addColumn(
                "{{%attachment}}",
                $this->addColumn,
                $this->string()->defaultValue(null)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%attachment}}");
        if (!empty($table->columns[$this->addColumn])) {
            $this->dropColumn(
                "{{%attachment}}",
                $this->addColumn
            );
        }
    }
}
