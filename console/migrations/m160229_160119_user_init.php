<?php

use yii\db\Migration;

class m160229_160119_user_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'access_token' => $this->string()->unique()->defaultValue(null),
            'email' => $this->string()->defaultValue(null),

            'first_name' => $this->string(100)->defaultValue(null),
            'last_name' => $this->string(100)->defaultValue(null),
            'middle_name' => $this->string(100)->defaultValue(null),
            'salary' => $this->float()->defaultValue(null),
            'reporting_salary' => $this->float()->defaultValue(null),
            'currency' => $this->integer()->defaultValue(null),


            'passport_number' => $this->string()->defaultValue(null),
            'vat' => $this->integer()->defaultValue(null),
            'interview' => $this->integer()->defaultValue(null),
            'resume' => $this->string()->defaultValue(null),
            'note' => $this->text()->defaultValue(null),
            'date_receipt' => $this->integer()->defaultValue(null),
            'date_dismissal' => $this->integer()->defaultValue(null),

            'role_id' => $this->integer()->notNull()->defaultValue(1),
            'position_id' => $this->integer()->defaultValue(null),
            'registration_id' => $this->integer()->defaultValue(null),
            'company_id' => $this->integer()->defaultValue(null),
            'country_id' => $this->integer()->defaultValue(null),
            'zip' => $this->string(10)->defaultValue(null),
            'city' => $this->string(100)->defaultValue(null),
            'address' => $this->string(100)->defaultValue(null),
            'phone' => $this->string(50)->defaultValue(null),
            'skype' => $this->string(50)->defaultValue(null),
            'avatar' => $this->string()->defaultValue(null),
            'birthday' => $this->integer()->defaultValue(null),
            'secret_birthday'=> $this->integer(1)->notNull(),
            'slogan'=> $this->text()->defaultValue(null),
            'like'=> $this->text()->defaultValue(null),
            'dislike'=> $this->text()->defaultValue(null),
            'gender' => $this->boolean()->notNull()->defaultValue(false),
            'verified' => $this->boolean()->notNull()->defaultValue(false),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            //'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'hired_at' => $this->integer()->defaultValue(null),
            'fired_at' => $this->integer()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),

            'last_login_at' => $this->integer()->defaultValue(null),
            'deleted' => $this->integer(1)->notNull(),
        ], $tableOptions);

        $this->addColumn('{{%user}}', 'facebook', $this->string(50)->defaultValue(null)->after('skype'));
        $this->addColumn('{{%user}}', 'linkedin', $this->string(50)->defaultValue(null)->after('facebook'));

        $this->createTable('{{%user_auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->string()->notNull(),
            'screen_name' => $this->string()->defaultValue(null),
        ], $tableOptions);

        $this->batchInsert('{{%user}}', ['id', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'access_token', 'email', 'first_name', 'last_name', 'role_id', 'position_id','country_id', 'zip', 'city', 'address', 'phone','skype' ,'avatar', 'birthday','secret_birthday', 'slogan', 'like', 'dislike','gender', 'verified', 'active', 'created_at', 'updated_at', 'last_login_at','deleted'], [
            [1, 'tanya', 'rqCWisc52mDl44St5u8wtbgKFy_WY7mB', '$2y$13$tzZOnOzfajkeyJgGkgVxP.6BpT6acIgzkgwpCMuMLV5m/aWxXfDEW', 'pZzT7l-F5IxXEPwZodB3EPTfVmrorT2r_1452986682', null, 'tanya.v.nazarchuk@gmail.com', 'Tanya', 'Nazarchuk', 1, 1,232, '6900', 'Zaporozie', 'Doroshenko 4-28', '096830870', null,null, null, 0, null, null,0, 1, 1, 1, 1450874841, 1452987623, 1452988429,0],
            [2, 'alex', 'vlrTzbhrwHQSsYIYE2N3PrFjqULGHdxQ', '$2y$13$OKfNrfLS.As/88bEt2wMledK5U8A3LHBNiVWmeBgDS80hF5K9y5K.', null, null, 'alex@gmail.com', 'Alex', 'Kotlyar', 2, 1, 232, '62000', 'Zaporozie', 'Ivanova 1-2', '11111111',null,null, null, 0, null, null,0, 0, 1, 1, 1452969958, 1452969958, null,0],
            [3, 'nadia', 'h564ys8a3GBmUQmB0i45jOkASeFzqmQ1', '$2y$13$oKzNEIZhmPIWh.7oO5QmB.nV/KR0mPN7uPS0wrb3l4BGvLlQ1TpFS', null, null, 'nadia@gmail.com', 'Nadia', 'Bezhan', 2, 1, 232, '62000', 'Zaporozie', 'Metalurgov 2-1', '22222222',null,null, null, 0, null, null,0, 1, 1, 1, 1452970230, 1452970230, null,0],
            [4, 'alexander', '8c7doH0keCIFweBRCe_RGpGKIgbsd8jP', '$2y$13$Jg1chJqPzxVHycC4fq/9sOY9i8peY0kvBaxhZWQ.EZBihcDoRuDRS', null, null, 'alexander@gmail.com', 'Alexander', 'Azartsev', 3, 1, 232, '62000', 'Zaporozie', 'Address 1-2', '11112222',null,null, null,0, null, null,0, 0, 1, 1, 1452970311, 1452970334, null,0],
            [6, 'artem', 'W8YgAhvfB0fIBiB00WGzFQpd9V8XbRkI', '$2y$13$p9KH0HDRay1VyGba8iJAT.mP/B6s55Qmo1Hu5H2Li6hOUiQKj6G.O', null, null, 'artem@gmail.com', 'Artem', 'Sakhan', 1, 1, 232, '62000', 'Zaporozie', 'Address 2-1', '22221111', null,null, null, 0,null, null,0, 0, 1, 1, 1452970575, 1452970575, null,0],
            [7, 'ilya', '3A3hKDgrwUd6DJyvSQvp8UFKTicuosnz', '$2y$13$hMG83Kf9XgFfW5IupS1yveWssEHDClUhUH5UUa1kiLPhZ41gIzqt.', null, null, 'ilya@gmail.com', 'Ilya', 'Ilya', 1, 1, 232, '62000', 'Zaporozie', 'Address 1-1', '22223333',null,null, null, 0, null, null,0, 0, 1, 1, 1452970644, 1452970772, null,0],
            [8, 'anna', 'xpK-jIY4xBDcpie5slryG3c5OlPcOphl', '$2y$13$E3Sx77gLsArzSPnhOnuR..ls1nSto0gqhyteQ0LOMXv8IzdYb2x8q', null, null, 'anna@gmail.com', 'Anna', 'Romancova', 3, 1, 232, '62000', 'Zaporozie', 'Address 3-3', '33333333',null,null, null, 0, null, null,0, 1, 1, 1, 1452970760, 1452970824, null,0],
            [9, 'tanya_nazarchyk', 'NK5Gc4iyF6Y8d8_DO7cMfsTkp-1SpZ0z', '', 'UXMggA0SW3Kx0thoKEwP2q5yx-5b7RD7_1452971685', null, null, 'Танюха', 'Назарчук', 1, 1, null, '', '', '', '', null, null, null, 0,null, null,0, 1, 1, 1, 1452971685, 1452971685, null,0],
            [10, 'tanya_nazarchyk1', 'goB6AuOBk28cJytzLAXwoHV5fObdpRw7', '', 'W2wxjny_elwYfIcgbuAPaAWZH1B3GaAZ_1452972049', null, 'tanya_nazarchyk@mail.ru', 'Tanya', 'Nazarchuk', 1, 1, null, '', '', '', '',null, null, null, 0,null, null, 0,1, 1, 1, 1452972049, 1452972049, null,0],
            [11, 'TNazarchyk', 'FopGX_zJetWlr5XzaBkXJK23kwvvJQmF', '', '84GzvkIOZWkw0eM-nnPYV84PwehjgRqd_1452972238', null, null, 'Tanya', ' Nazarchyk', 1, 1, null, '', '', '', '',null,null, null, 0, null, null, 0, 0, 1, 1, 1452972238, 1452972238, null,0],
            [12, 'yaroslav', 'YdGN6xMgNew2Fy8sEtF0GE_ukviWnzIO', '$2y$13$Yev98zik1BekRma3e6SPbe47b8EnSus.38qMtf0F1DXUUAvtPlYvq', null, null, 'yaroslav@gmail.com', 'Yaroslav', 'Lutskyi', 2, 1, null, '', '', '', '11113333',null, null, null, 0,null, null, 0, 0, 1, 1, 1457272316, 1457272316, null,0],
            [13, 'dima', 'SWYRgM0s2_8-un9R0OnfUIXEXWmtt7eI', '$2y$13$8HIKJJSEWwxIbbuAHM7vDe8oO1POpzgrZFZr3j4XV0/zsfLxFoErO', null, null, 'dima@gmail.com', 'Dima', 'Dima', 2, 1, null, '', '', '', '44441111',null,null, null, 0, null, null,0, 0, 1, 1, 1457272398, 1457272398, null,0],
            [14, 'demo', 'rhCbK0fGb3D_gUBe6lOtYT6ESzjM9_UW', '$2y$13$j9ptu97LepvafcH572tbhOHZrc.EvvTI9VC878guh/vrCyXnRcOOa', null, null, 'demo@gmail.com', 'Demo', 'Demo', 2, 1, null, '', '', '', '',null, null, null, 0, null, null,0, 0, 1, 1, 1460381778, 1460448744, 1460448755,0],
        ]);

        $this->batchInsert('{{%user_auth}}', ['id', 'user_id', 'source', 'source_id', 'screen_name'], [
            [2, 10, 'facebook', '879252018862360', null],
            [3, 11, 'twitter', '4792584983', 'TNazarchyk'],
            [4, 1, 'google', '110765284483580761046', null],
        ]);

        $this->addForeignKey('fk_user_auth_user', '{{%user_auth}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_auth_user', '{{%user_auth}}');

        $this->dropTable('{{%user}}');
        $this->dropTable('{{%user_auth}}');
    }
}
