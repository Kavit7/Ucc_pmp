<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "property_price".
 *
 * @property int $id
 * @property string $uuid
 * @property float $unit_amount
 * @property string $period
 * @property float $min_monthly_rent
 * @property float $max_monthly_rent
 * @property int $property_id
 * @property string $price_type
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Property $property
 */
class PropertyPrice extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'unit_amount', 'period', 'min_monthly_rent', 'max_monthly_rent', 'property_id', 'price_type'], 'required'],
            [['unit_amount', 'min_monthly_rent', 'max_monthly_rent'], 'number'],
            [['property_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid', 'price_type', 'period'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'UUID',
            'unit_amount' => 'Unit Amount',
            'period' => 'Period',
            'min_monthly_rent' => 'Min Monthly Rent',
            'max_monthly_rent' => 'Max Monthly Rent',
            'property_id' => 'Property',
            'price_type' => 'Price Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Relation to Property
     */
    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }
}
