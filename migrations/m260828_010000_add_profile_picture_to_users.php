<?php

use yii\db\Migration;

/**
 * No-op. `users.profile_picture` is now created directly by
 * m260827_000000_create_base_schema. Left in place, unchanged in name, only so the
 * `migration` table's existing "applied" record for this class stays
 * valid on databases that already ran it.
 */
class m260828_010000_add_profile_picture_to_users extends Migration
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
