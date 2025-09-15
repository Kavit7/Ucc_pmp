<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "district".
 *
 * @property int $district_id
 * @property string $uuid
 * @property int $region_id
 * @property string $district_name
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property Users $createdBy
 * @property Location[] $locations
 * @property Region $region
 * @property Street[] $streets
 * @property Users $updatedBy
 * @property Ward[] $wards
 */
class District extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'district';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [[ 'region_id', 'district_name'], 'required'],
            [['region_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['district_name'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'region_id']],
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
            'district_id' => 'District ID',
            'uuid' => 'Uuid',
            'region_id' => 'Region Name',
            'district_name' => 'District Name',
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
     * Gets query for [[Locations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::class, ['district_id' => 'district_id']);
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
     * Gets query for [[Streets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreets()
    {
        return $this->hasMany(Street::class, ['district_id' => 'district_id']);
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
        return $this->hasMany(Ward::class, ['district_id' => 'district_id']);
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
        ->where(['like', 'uuid', 'Dis_%', false])
        ->scalar();

    $lastNumber = $lastNumber ?: 0;

    do {
        $lastNumber++;
        $newUuid = 'Dis_' . $lastNumber;
    } while (self::find()->where(['uuid' => $newUuid])->exists());

    $this->uuid = $newUuid;
}

        }
        return true;
    }
    return false;
}

}
