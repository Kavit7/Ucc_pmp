<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property string $uuid
 * @property int $property_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $message
 * @property string $created_at
 *
 * @property Property $property
 */
class PropertyInquiry extends ActiveRecord
{
    public static function tableName()
    {
        return 'property_inquiry';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function () { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['property_id', 'name', 'email'], 'required'],
            [['property_id'], 'integer'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['uuid'], 'string', 'max' => 100],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::class, 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->uuid)) {
                $this->uuid = Yii::$app->security->generateRandomString(32);
            }
            return true;
        }
        return false;
    }

    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }
}
