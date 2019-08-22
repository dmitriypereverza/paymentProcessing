<?php

use yii\db\Migration;

class m190822_192450_create_user_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_wallet', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'sum' => $this->money(10, 2)->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_wallet');
    }
}
