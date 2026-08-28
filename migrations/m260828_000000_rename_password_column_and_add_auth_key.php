<?php

use yii\db\Migration;

/**
 * Renames `users`.`password` to `password_hash` for clarity (the column
 * stores a bcrypt hash, never a plaintext password) and adds `auth_key`,
 * which yii\web\IdentityInterface::getAuthKey() requires for "remember me"
 * cookie logins to work.
 */
class m260828_000000_rename_password_column_and_add_auth_key extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('{{%users}}', 'password', 'password_hash');
        $this->addColumn('{{%users}}', 'auth_key', $this->string(32)->null()->after('password_hash'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'auth_key');
        $this->renameColumn('{{%users}}', 'password_hash', 'password');
    }
}
