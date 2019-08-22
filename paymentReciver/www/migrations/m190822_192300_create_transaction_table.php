<?php

use yii\db\Migration;

class m190822_192300_create_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transaction', [
            'id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'sum' => $this->money(10, 2)->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('transaction');
    }
}
