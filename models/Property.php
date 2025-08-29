<?php
namespace app\models;

use yii\db\ActiveRecord;

class Property extends ActiveRecord
{
    public static function tableName()
    {
        return 'property'; // hakikisha jina la table DB ni property
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }
}
