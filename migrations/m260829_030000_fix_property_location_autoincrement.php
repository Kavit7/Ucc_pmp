<?php

use yii\db\Migration;

/**
 * No-op. `property_location.id` is now created with AUTO_INCREMENT
 * directly by m260827_000000_create_base_schema. Left in place, unchanged in name,
 * only so the `migration` table's existing "applied" record for this
 * class stays valid on databases that already ran it.
 */
class m260829_030000_fix_property_location_autoincrement extends Migration
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
