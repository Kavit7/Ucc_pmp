<?php

use yii\db\Migration;

/**
 * Adds TOTP-based two-factor authentication support to users: a secret
 * (set once, when 2FA is first enabled) and a flag the login flow checks
 * to decide whether to prompt for a code after a successful password
 * check.
 */
class m260907_000000_add_2fa_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'totp_secret', $this->string(64)->null()->after('password_hash'));
        $this->addColumn('{{%users}}', 'totp_enabled', $this->boolean()->notNull()->defaultValue(0)->after('totp_secret'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'totp_enabled');
        $this->dropColumn('{{%users}}', 'totp_secret');
    }
}
