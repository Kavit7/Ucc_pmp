<?php

use yii\db\Migration;

/**
 * No-op. `users.password_hash` and `users.auth_key` are now created
 * directly by m260827_000000_create_base_schema (the base-schema migration was
 * reconstructed after this file's changes had already been folded into
 * the live schema). Left in place, unchanged in name, only so the
 * `migration` table's existing "applied" record for this class stays
 * valid on databases that already ran it.
 */
class m260828_000000_rename_password_column_and_add_auth_key extends Migration
{
    public function safeUp()
    {
        return true;
    }

    public function safeDown()
    {
        return true;
    }
}
