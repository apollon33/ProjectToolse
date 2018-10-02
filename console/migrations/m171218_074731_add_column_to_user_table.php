<?php

use yii\db\Migration;

/**
 * Class m171218_074731_add_column_to_user_table
 */
class m171218_074731_add_column_to_user_table extends Migration
{
    private $updateTableUser = 'user';
    private $addColumn = 'color';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!in_array($this->updateTableUser, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%user}}");
        if (empty($table->columns[$this->addColumn])) {
            $this->addColumn(
                "{{%user}}",
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
        $table = Yii::$app->db->schema->getTableSchema("{{%user}}");
        if (!empty($table->columns[$this->addColumn])) {
            $this->dropColumn(
                "{{%user}}",
                $this->addColumn
            );
        }
    }
}
