<?php

use yii\db\Migration;
use yii\db\Expression;
use modules\document\models\Document;

/**
 * Class m180116_122524_add_colomn_to_document
 */
class m180116_122524_add_colomn_to_document extends Migration
{
    private $updateTableDocument = 'document';
    private $addColumn = 'order';
    private $rootColumn = 'root';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!in_array($this->updateTableDocument, $this->getDb()->schema->tableNames)) {
            return;
        }

        $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableDocument}}");
        if (empty($table->columns[$this->addColumn])) {
            $this->addColumn(
                "{{%$this->updateTableDocument}}",
                $this->addColumn,
                $this->integer(11)->defaultValue(0)
            );
        }

        $documents = Document::find()->select('id')->where(['lvl' => 0])->asArray()->all();
        foreach ($documents as $key => $document) {
            $this->update($this->updateTableDocument,
                [$this->addColumn => new Expression('`order`' . sprintf('%+d',  $key + 1))],
                ['=', $this->rootColumn, $document['id']]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if (!in_array($this->updateTableDocument, $this->getDb()->schema->tableNames)) {
            return;
        }
        $table = Yii::$app->db->schema->getTableSchema("{{%$this->updateTableDocument}}");
        if (!empty($table->columns[$this->addColumn])) {
            $this->dropColumn(
                "{{%$this->updateTableDocument}}",
                $this->addColumn
            );
        }
    }
}
