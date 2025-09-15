<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property int $country_id
 * @property string $uuid
 * @property string $country_name
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property Users $createdBy
 * @property Location[] $locations
 * @property Region[] $regions
 * @property Users $updatedBy
 */
class Country extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'default', 'value' => null],
            [['uuid', 'country_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['uuid'], 'string', 'max' => 100],
            [['country_name'], 'string', 'max' => 255],
            [['uuid'], 'unique'],
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
            'country_id' => 'Country ID',
            'uuid' => 'Uuid',
            'country_name' => 'Country Name',
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
        return $this->hasMany(Location::class, ['country_id' => 'country_id']);
    }

    /**
     * Gets query for [[Regions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::class, ['country_id' => 'country_id']);
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
        ->where(['like', 'uuid', 'Cou_%', false])
        ->scalar();

    $lastNumber = $lastNumber ?: 0;

    do {
        $lastNumber++;
        $newUuid = 'Cou_' . $lastNumber;
    } while (self::find()->where(['uuid' => $newUuid])->exists());

    $this->uuid = $newUuid;
}

        }
        return true;
    }
    return false;
}

}
