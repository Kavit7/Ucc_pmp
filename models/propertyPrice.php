<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Property;
use app\models\ListSource;

class PropertyPrice extends ActiveRecord
{
    public static function tableName()
    {
        return 'property_price';
    }

    public function rules()
    {
        return [
            [['unit_amount', 'property_id', 'price_type'], 'required'],
            [['unit_amount', 'min_monthly_rent', 'max_monthly_rent'], 'number'],
            [['property_id', 'price_type', 'created_by', 'updated_by'], 'integer'],
            [['period'], 'string', 'max' => 50],
            [['uuid'], 'string', 'max' => 100],
            [['uuid'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

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

    // Relation to Property
    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }
     public function getPropertyPrice()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }
    // Relation to price type (ListSource)
    public function getPriceTypeName()
    {
        return $this->hasOne(ListSource::class, ['id' => 'price_type']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                // generate UUID if new record
                $this->uuid = Yii::$app->security->generateRandomString(36);
                $this->created_by = Yii::$app->user->id ?? null;
            }
            $this->updated_by = Yii::$app->user->id ?? null;
            return true;
        }
        return false;
    }
}