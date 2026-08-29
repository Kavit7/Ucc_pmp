<?php

use yii\db\Migration;

/**
 * property_location.id was created as a plain INT primary key with no
 * AUTO_INCREMENT, which forced the model to treat it as a required,
 * user-supplied attribute. Since the Gii-generated create/update forms
 * never render an id field, every create silently failed validation.
 * This aligns the column with every other table in the app (auto-increment
 * PK, server-assigned).
 */
class m260829_030000_fix_property_location_autoincrement extends Migration
{
    public function safeUp()
    {
        // The column is already the PRIMARY KEY, so we only need to add
        // AUTO_INCREMENT - re-declaring PRIMARY KEY via alterColumn() would
        // conflict with the existing constraint.
        $this->execute('ALTER TABLE `property_location` MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE `property_location` MODIFY `id` INT(11) NOT NULL');
    }
}
