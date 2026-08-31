<?php

use yii\db\Migration;

class m260830_000000_add_password_reset_token extends Migration
{
    public function safeUp()
    {
        $this->addColumn('users', 'password_reset_token', $this->string(255)->unique()->after('auth_key'));
    }

    public function safeDown()
    {
        $this->dropColumn('users', 'password_reset_token');
    }
}
