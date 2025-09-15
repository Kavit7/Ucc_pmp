<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property_extra_data".
 *
 * @property int $id
 * @property string $uuid
 * @property int $property_id
 * @property int $attribute_answer_id
 * @property string|null $attribute_answer_text
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property PropertyAttributeAnswer $attributeAnswer
 * @property Users $createdBy
 * @property Property $property
 * @property Users $updatedBy
 */
class PropertyExtraData extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property_extra_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_answer_text', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['uuid', 'property_id', 'attribute_answer_id'], 'required'],
            [['property_id', 'attribute_answer_id', 'created_by', 'updated_by'], 'integer'],
            [['attribute_answer_text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['uuid'], 'unique'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::class, 'targetAttribute' => ['property_id' => 'id']],
            [['attribute_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' =>  PropertyAttributeAnswer::class, 'targetAttribute' => ['attribute_answer_id' => 'id']],
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
            'property_id' => 'Property ID',
            'attribute_answer_id' => 'Attribute Answer ID',
            'attribute_answer_text' => 'Attribute Answer Text',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[AttributeAnswer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeAnswer()
    {
        return $this->hasOne(PropertyAttributeAnswer::class, ['id' => 'attribute_answer_id']);
    }
    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }
  public function getPropertyAttribute(){
    return $this->hasOne(PropertyAttribute::class,['id'=>'property_attribute_id']);
  }
    

}
