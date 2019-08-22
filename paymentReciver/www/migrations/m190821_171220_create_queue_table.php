<?php

use yii\db\Migration;

class m190821_171220_create_queue_table extends Migration {

    public function safeUp() {
        $this->createTable('queue', [
            'id' => $this->primaryKey(),
            'content' => $this->string()->notNull(),
            'success' => $this->boolean()->defaultValue(null),
            'inProgress' => $this->boolean()->defaultValue(false),
        ]);
    }

    public function safeDown() {
        $this->dropTable('queue');
    }
}
