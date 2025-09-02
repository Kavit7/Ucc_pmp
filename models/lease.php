<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Lease extends ActiveRecord
{
    public static function tableName()
    {
        return 'lease';
    }

    public function rules()
    {
        return [
            [['uuid', 'property_id', 'tenant_id', 'property_price_id', 'status', 'lease_start_date', 'lease_end_date'], 'required'],
            [['property_id', 'tenant_id', 'property_price_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['lease_doc_url'], 'string'],
            [['uuid'], 'string', 'max' => 100],
            [['lease_start_date', 'lease_end_date'], 'safe'], // for date fields
        ];
    }

    // Virtual property to calculate duration in months
    public function getDurationMonths()
    {
        if ($this->lease_start_date && $this->lease_end_date) {
            $start = new \DateTime($this->lease_start_date);
            $end = new \DateTime($this->lease_end_date);
            return ($end->format('Y') - $start->format('Y')) * 12 + ($end->format('m') - $start->format('m'));
        }
        return null;
    }

    // Relations
    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    public function getTenant()
    {
        return $this->hasOne(Tenant::class, ['id' => 'tenant_id']);
    }

    public function getPropertyPrice()
    {
        return $this->hasOne(PropertyPrice::class, ['id' => 'property_price_id']);
    }
}
