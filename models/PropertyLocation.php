<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property_location".
 *
 * @property int $id
 * @property int|null $location_id
 * @property int|null $property_id
 * @property int|null $status_id
 *
 * @property Location $location
 * @property Property $property
 * @property ListSource $status
 */
class PropertyLocation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location_id', 'property_id', 'status_id'], 'default', 'value' => null],
            [['id'], 'required'],
            [['id', 'location_id', 'property_id', 'status_id'], 'integer'],
            [['id'], 'unique'],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::class, 'targetAttribute' => ['property_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'location_id' => 'Location ID',
            'property_id' => 'Property ID',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Property]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(ListSource::class, ['id' => 'status_id']);
    }

}
