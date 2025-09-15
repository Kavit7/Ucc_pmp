<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "street".
 *
 * @property int $street_id
 * @property string $uuid
 * @property string $street_name
 * @property int $region_id
 * @property int $district_id
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property Users $createdBy
 * @property District $district
 * @property Location[] $locations
 * @property Property[] $properties
 * @property Region $region
 * @property Users $updatedBy
 */
class Street extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'street';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [[ 'street_name', 'region_id', 'district_id'], 'required'],
            [['region_id', 'district_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['street_name'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'region_id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::class, 'targetAttribute' => ['district_id' => 'district_id']],
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
            'street_id' => 'Street ID',
            'uuid' => 'Uuid',
            'street_name' => 'Street Name',
            'region_id' => 'Region Name',
            'district_id' => 'District Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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
     * Gets query for [[District]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::class, ['district_id' => 'district_id']);
    }

    /**
     * Gets query for [[Locations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::class, ['street_id' => 'street_id']);
    }

    /**
     * Gets query for [[Properties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::class, ['street_id' => 'street_id']);
    }

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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'updated_by']);
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
        ->where(['like', 'uuid', 'Stre_%', false])
        ->scalar();

    $lastNumber = $lastNumber ?: 0;

    do {
        $lastNumber++;
        $newUuid = 'Stre_' . $lastNumber;
    } while (self::find()->where(['uuid' => $newUuid])->exists());

    $this->uuid = $newUuid;
}

        }
        return true;
    }
    return false;
}
}
