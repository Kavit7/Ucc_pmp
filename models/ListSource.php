<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ListSource extends ActiveRecord
{
    public static function tableName()
    {
        return 'list_source';
    }

    public function rules()
    {
        return [
            [['uuid', 'list_Name', 'code','category'], 'required'],
            [['description'], 'string'],
            [['parent_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid', 'category'], 'string', 'max' => 100],
            [['list_Name', 'code', 'sort_by'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
            ['parent_id', 'validateParent'], // custom validation
        ];
    }

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

    // Relations
    public function getParent()
    {
        return $this->hasOne(ListSource::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(ListSource::class, ['parent_id' => 'id']);
    }

    // Generate UUID, code, sort, parent before validation
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->isNewRecord) {
            if (empty($this->uuid)) {
                $lastUuid = static::find()
        ->select('uuid')
        ->where(['like', 'uuid', 'List_%', false])
        ->orderBy(['id' => SORT_DESC])
        ->scalar();

    if ($lastUuid) {
        $lastNumber = (int)str_replace('List_', '', $lastUuid);
        $this->uuid = 'List_' . ($lastNumber + 1);
    } else {
        $this->uuid = 'List_1';
    }
            }

            if (empty($this->code)) {
                $this->code = $this->generateCode();
            }

            if (empty($this->sort_by)) {
                $this->sort_by = $this->generateSort();
            }

            if (!isset($this->parent_id)) {
                $this->parent_id = $this->generateParentId();
            }
        }

        return true;
    }

    // Timestamps
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by = Yii::$app->user->id ?? 1;
        }

        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_by = Yii::$app->user->id ?? 1;

        return true;
    }

    // Generate random code
    public function generateCode()
    {
    do {
        $code = 'LIST' . rand(100, 999);
    } while (self::find()->where(['code' => $code])->exists());
    return $code;
    }

    // Generate sort order as string
    public function generateSort()
    {
        $maxSort = self::find()->max('sort_by');
        $nextSort = $maxSort ? $maxSort + 1 : 1;
        return (string)$nextSort;
    }

    // Generate parent ID
    public function generateParentId()
    {
        return null; // top-level by default
    }
}
