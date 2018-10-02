<?php

use yii\db\Migration;

/**
 * Class m180102_115920_change_column_vat_in_user
 */
class m180102_115920_change_column_vat_in_user extends Migration
{

    private $updateTableUser = 'user';
    private $alterColumn = 'vat';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn(
            "{{%$this->updateTableUser}}",
            $this->alterColumn,
            $this->string(50)->defaultValue(null)
        );
    }

}
