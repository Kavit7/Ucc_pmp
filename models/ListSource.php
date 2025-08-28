<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "list_source".
 *
 * @property int $id
 * @property string $uuid
 * @property string $list_Name
 * @property string $code
 * @property string|null $category
 * @property string|null $sort_by
 * @property string|null $description
 * @property int|null $parent_id
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 */
class ListSource extends ActiveRecord
{
    public static function tableName()
    {
        return 'list_source';
    }

    public function rules()
{
    return [
        [['uuid', 'list_Name', 'code'], 'required'],
        [['description'], 'string'],
        [['parent_id', 'created_by', 'updated_by'], 'integer'],
        [['created_at', 'updated_at'], 'safe'],
        [['uuid', 'category'], 'string', 'max' => 100],
        [['list_Name', 'code', 'sort_by'], 'string', 'max' => 255],
        [['uuid'], 'unique'],
        ['parent_id', 'validateParent'], // custom validation
    ];
}

// Custom validation rule
public function validateParent($attribute)
{
    if ($this->parent_id !== null) {
        $parent = ListSource::findOne($this->parent_id);
        if ($parent === null) {
            $this->addError($attribute, 'Invalid parent selected.');
        }
    }
}


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'UUID',
            'list_Name' => 'List Name',
            'code' => 'Code',
            'category' => 'Category',
            'sort_by' => 'Sort By',
            'description' => 'Description',
            'parent_id' => 'Parent ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    // Relation to parent
    public function getParent()
    {
        return $this->hasOne(ListSource::class, ['id' => 'parent_id']);
    }

    // Relation to children
    public function getChildren()
    {
        return $this->hasMany(ListSource::class, ['parent_id' => 'id']);
    }

    // Automatically set UUID and timestamps
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->uuid = Yii::$app->security->generateRandomString(32);
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->id ?? 1; // default admin
            }
            $this->updated_at = date('Y-m-d H:i:s');
            $this->updated_by = Yii::$app->user->id ?? 1;
            return true;
        }
        return false;
    }
}
