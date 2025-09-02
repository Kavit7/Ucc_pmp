<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property_attribute_answer".
 *
 * @property int $id
 * @property string $uuid
 * @property int $property_attribute_id
 * @property int $answer_id
 * @property string|null $status
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property ListSource $answer
 * @property Users $createdBy
 * @property PropertyAttribute $propertyAttribute
 * @property PropertyExtraData[] $propertyExtraDatas
 * @property Users $updatedBy
 */
class PropertyAttributeAnswer extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property_attribute_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['uuid', 'property_attribute_id', 'answer_id'], 'required'],
            [['property_attribute_id', 'answer_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 50],
            [['uuid'], 'unique'],
            [['property_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyAttribute::class, 'targetAttribute' => ['property_attribute_id' => 'id']],
            [['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['answer_id' => 'id']],
            /*[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],*/
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
            'property_attribute_id' => 'Property Attribute ID',
            'answer_id' => 'Answer ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Answer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(ListSource::class, ['id' => 'answer_id']);
    }

 
  
    public function getPropertyAttribute()
    {
        return $this->hasOne(PropertyAttribute::class, ['id' => 'property_attribute_id']);
    }

    /**
     * Gets query for [[PropertyExtraDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyExtraDatas()
    {
        return $this->hasMany(PropertyExtraData::class, ['attribute_answer_id' => 'id']);
    }

    
    
   

}
