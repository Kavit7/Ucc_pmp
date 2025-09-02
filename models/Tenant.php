<?php
namespace app\models;

use yii\db\ActiveRecord;

class Tenant extends ActiveRecord
{
    public static function tableName()
    {
        return 'tenant'; // hakikisha jina la table DB ni tenant
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }
}
