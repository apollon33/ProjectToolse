<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attachment_document`.
 * Has foreign keys to the tables:
 *
 * - `attachment`
 * - `document`
 */
class m180130_114022_create_junction_table_for_attachment_and_document_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('attachment_document', [
            'attachment_id' => $this->integer(),
            'document_id' => $this->integer(),
            'PRIMARY KEY(attachment_id, document_id)',
        ]);
        $this->addForeignKey(
            'fk-attachment_document-attachment_id',
            'attachment_document',
            'attachment_id',
            'attachment',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-attachment_document-document_id',
            'attachment_document',
            'document_id',
            'document',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-attachment_document-attachment_id',
            'attachment_document'
        );
        $this->dropForeignKey(
            'fk-attachment_document-document_id',
            'attachment_document'
        );
        $this->dropTable('attachment_document');
    }
}
