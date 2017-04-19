<?php

use yii\db\Migration;

class m170418_161109_addFkMeaasge extends Migration
{
    public function up()
    {
        $this->addForeignKey('message_chat_id_fk','message',  'chat_id', 'chat', 'id', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('message_chat_id_fk', 'message');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
