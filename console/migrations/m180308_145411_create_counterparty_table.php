<?php

use yii\db\Migration;

/**
 * Handles the creation of table `counterparty`.
 */
class m180308_145411_create_counterparty_table extends Migration
{

    private $id = 'id';
    private $name = 'name';
    private $type = 'type';
    private $registration_number = 'registration_number';
    private $vat = 'vat';
    private $timezone = 'timezone';
    private $country = 'country';
    private $city = 'city';
    private $address = 'address';
    private $payment_method = 'payment_method';
    private $currency = 'currency';
    private $bank_name = 'bank_name';
    private $iban = 'iban';
    private $swift = 'swift';
    private $comments = 'comments';
    private $client_id = 'client_id';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('counterparty', [
            'id' => $this->primaryKey(),
            $this->id => $this->primaryKey(),
            $this->name => $this->string()->defaultValue(null),
            $this->type => $this->string()->defaultValue(null),
            $this->registration_number => $this->string()->defaultValue(null),
            $this->vat => $this->string()->defaultValue(null),
            $this->timezone => $this->string()->defaultValue(null),
            $this->country => $this->string()->defaultValue(null),
            $this->city => $this->string()->defaultValue(null),
            $this->address => $this->string()->defaultValue(null),
            $this->payment_method => $this->string()->defaultValue(null),
            $this->currency => $this->string()->defaultValue(null),
            $this->bank_name => $this->string()->defaultValue(null),
            $this->iban => $this->string()->defaultValue(null),
            $this->swift => $this->string()->defaultValue(null),
            $this->comments => $this->text(),
            $this->client_id => $this->integer(11)->defaultValue(null),

        ]);

        $this->addForeignKey('fk_client_counterparty', '{{%counterparty}}', 'client_id', '{{%client}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_client_counterparty', '{{%counterparty}}');
        $this->dropTable('{{%counterparty}}');
    }
}
