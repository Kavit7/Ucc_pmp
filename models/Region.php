<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $region_id
 * @property string $uuid
 * @property string $name
 * @property int $country_id
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property Country $country
 * @property Users $createdBy
 * @property District[] $districts
 * @property Location[] $locations
 * @property Street[] $streets
 * @property Users $updatedBy
 * @property Ward[] $wards
 */
class Region extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [[ 'name', 'country_id'], 'required'],
            [['country_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['country_id' => 'country_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'region_id' => 'Region ID',
            'uuid' => 'Uuid',
            'name' => 'Region Name',
            'country_id' => 'Country ID',
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
    public function getCreatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'created_by']);
    }

    /**
     * Gets query for [[Districts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(District::class, ['region_id' => 'region_id']);
    }

    /**
     * Gets query for [[Locations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::class, ['region_id' => 'region_id']);
    }

    /**
     * Gets query for [[Streets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreets()
    {
        return $this->hasMany(Street::class, ['region_id' => 'region_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'updated_by']);
    }

    /**
     * Gets query for [[Wards]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWards()
    {
        return $this->hasMany(Ward::class, ['region_id' => 'region_id']);
    }
  public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        if ($insert) {
            // Set the user who created the record
            $this->created_by = Yii::$app->user->id;
            $this->updated_by=Yii::$app->user->id;
            // Generate UUID if empty
           if (empty($this->uuid)) {
    $lastNumber = (int) self::find()
        ->select(['MAX(CAST(SUBSTRING(uuid,6) AS UNSIGNED)) AS maxNumber'])
        ->where(['like', 'uuid', 'Reg_%', false])
        ->scalar();

    $lastNumber = $lastNumber ?: 0;

    do {
        $lastNumber++;
        $newUuid = 'Reg_' . $lastNumber;
    } while (self::find()->where(['uuid' => $newUuid])->exists());

    $this->uuid = $newUuid;
}

        }
        return true;
    }
    return false;
}



}
