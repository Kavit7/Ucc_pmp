<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $uuid
 * @property int $country_id
 * @property int $district_id
 * @property int $region_id
 * @property int $street_id
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property Country $country
 * @property Users $createdBy
 * @property District $district
 * @property PropertyLocation[] $propertyLocations
 * @property Region $region
 * @property Street $street
 * @property Users $updatedBy
 */
class Location extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * {@inheritdoc}
     */
public function rules()
{
    return [
        [['created_by', 'updated_by'], 'default', 'value' => null],
        [['uuid', 'country_id', 'district_id', 'region_id', 'street_id'], 'required'],
        [['country_id', 'district_id', 'region_id', 'street_id', 'created_by', 'updated_by'], 'integer'],
        [['created_at', 'updated_at'], 'safe'],
        [['uuid'], 'string', 'max' => 100],
        [['uuid'], 'unique'],
        [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['country_id' => 'country_id']],
        [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::class, 'targetAttribute' => ['district_id' => 'district_id']],
        [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'region_id']],
        [['street_id'], 'exist', 'skipOnError' => true, 'targetClass' => Street::class, 'targetAttribute' => ['street_id' => 'street_id']],
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
            'country_id' => 'Country ID',
            'district_id' => 'District ID',
            'region_id' => 'Region ID',
            'street_id' => 'Street ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['country_id' => 'country_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    //public function getCreatedBy()
    //{
       // return $this->hasOne(Users::class, ['user_id' => 'created_by']);
   // }

    /**
     * Gets query for [[District]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::class, ['district_id' => 'district_id']);
    }

    /**
     * Gets query for [[PropertyLocations]].
     *
     * @return \yii\db\ActiveQuery
     */
   // public function getPropertyLocations()
    //{
       // return $this->hasMany(PropertyLocation::class, ['location_id' => 'id']);
//}

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['region_id' => 'region_id']);
    }

    /**
     * Gets query for [[Street]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreet()
    {
        return $this->hasOne(Street::class, ['street_id' => 'street_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
 //   public function getUpdatedBy()
 //   {
   //     return $this->hasOne(Users::class, ['user_id' => 'updated_by']);
    //}
    public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {

        // Generate UUID only for new records
        if ($insert && empty($this->uuid)) {
            $lastUuid = self::find()
                ->select('uuid')
                ->where(['like', 'uuid', 'Loc_%', false])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();

            $this->uuid = $lastUuid
                ? 'Loc_' . ((int)str_replace('Loc_', '', $lastUuid) + 1)
                : 'Loc_1';
        }

        // Optionally, set created_at and updated_at
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by = \Yii::$app->user->id ?? null;
        }
        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_by = \Yii::$app->user->id ?? null;

        return true;
    }
    return false;
}


}
