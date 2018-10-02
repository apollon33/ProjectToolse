<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attachment_document`.
 */
class m180112_134409_create_attachment_document_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%attachment_document}}', [
            'attachment_id' => $this->integer(11)->notNull(),
            'document_id' => $this->integer(11)->notNull(),
            'version' => $this->smallInteger()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('fk_doc_attach_attachment', '{{%attachment_document}}', 'attachment_id', '{{%attachment}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_doc_attach_document', '{{%attachment_document}}', 'document_id', '{{%document}}', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_doc_attach_attachment', '{{%attachment_document}}');
        $this->dropForeignKey('fk_doc_attach_document', '{{%attachment_document}}');
        $this->dropTable('{{%attachment_document}}');
    }
}
