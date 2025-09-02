<?php

namespace app\models;
use app\models\ListSource;
use Yii;

/**
 * This is the model class for table "property_attribute".
 *
 * @property int $id
 * @property string $uuid
 * @property string $attribute_name
 * @property int $attribute_name_dataType_id
 * @property int|null $property_type_id
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property ListSource $attributeNameDataType
 * @property Users $createdBy
 * @property PropertyAttributeAnswer[] $propertyAttributeAnswers
 * @property ListSource $propertyType
 * @property Users $updatedBy
 */
class PropertyAttribute extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property_attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['property_type_id', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['uuid', 'attribute_name', 'attribute_name_dataType_id'], 'required'],
            [['attribute_name_dataType_id', 'property_type_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['attribute_name'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
            [['attribute_name_dataType_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['attribute_name_dataType_id' => 'id']],
          /*  [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],*/
            [['property_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['property_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'attribute_name' => 'Attribute Name',
            'attribute_name_dataType_id' => 'Attribute Name Data Type',
            'property_type_id' => 'Property Type ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
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
public function getPropertyType()
{
    return $this->hasOne(ListSource::class, ['id' => 'property_type_id']);
}

public function getDataType()
{
    return $this->hasOne(ListSource::class, ['id' => 'attribute_name_dataType_id']);
}


   
    
}
