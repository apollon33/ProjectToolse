<?php

use yii\db\Migration;

/**
 * Class m180305_105729_change_client_table
 */
class m180305_105729_change_client_table extends Migration
{
    private $first_name = 'first_name';
    private $last_name = 'last_name';
    private $company_name = 'company_name';
    private $timezone = 'timezone';
    private $client_name = 'client_name';
    private $deleted = 'deleted';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn(
            "{{client}}",
            $this->first_name
        );
        $this->dropColumn(
            "{{client}}",
            $this->last_name
        );
        $this->addColumn(
            "{{client}}",
            $this->client_name,
            $this->string(64)->defaultValue(null)
        );
        $this->addColumn(
            "{{client}}",
            $this->timezone,
            $this->string(64)->defaultValue(null)
        );
        $this->addColumn(
            "{{client}}",
            $this->company_name,
            $this->string(64)->defaultValue(null)
        );
        $this->dropColumn(
            "{{client}}",
            $this->deleted
        );
        $this->addColumn(
            "{{client}}",
            $this->deleted,
            $this->integer(1)->defaultValue(0)
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->addColumn(
            "{{client}}",
            $this->first_name,
            $this->string(100)->defaultValue(null)
        );
        $this->addColumn(
            "client",
            $this->last_name,
            $this->string(100)->defaultValue(null)
        );
        $this->dropColumn(
            "{{client}}",
            $this->client_name
        );
        $this->dropColumn(
            "{{client}}",
            $this->timezone
        );
        $this->dropColumn(
            "{{client}}",
            $this->company_name
        );
        $this->dropColumn(
            "client",
            $this->deleted
        );
        $this->addColumn(
            "client",
            $this->deleted,
            $this->integer(1)->defaultValue(null)
        );
    }
}

