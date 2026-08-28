<?php

use yii\db\Migration;

/**
 * Adds `users`.`profile_picture` — referenced already by the header avatar
 * and profile page (views/layouts/custom.php, views/custom/profile.php)
 * but never had a backing column.
 */
class m260828_010000_add_profile_picture_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'profile_picture', $this->string(255)->null()->after('phone'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'profile_picture');
    }
}
