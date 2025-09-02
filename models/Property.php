<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property".
 *
 * @property int $id
 * @property string $uuid
 * @property string $property_name
 * @property int $property_type_id
 * @property string|null $description
 * @property string|null $identifier_code
 * @property int|null $street_id
 * @property int $property_status_id
 * @property string|null $document_url
 * @property int $ownership_type_id
 * @property int $usage_type_id
 * @property int|null $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property Users $createdBy
 * @property Expense[] $expenses
 * @property Lease[] $leases
 * @property ListSource $ownershipType
 * @property PropertyExtraData[] $propertyExtraDatas
 * @property PropertyLocation[] $propertyLocations
 * @property PropertyPrice[] $propertyPrices
 * @property ListSource $propertyStatus
 * @property ListSource $propertyType
 * @property Street $street
 * @property Users $updatedBy
 * @property ListSource $usageType
 */
class Property extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'identifier_code', 'street_id', 'document_url', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['uuid', 'property_name', 'property_type_id', 'property_status_id', 'ownership_type_id', 'usage_type_id','identifier_code'], 'required'],
            [['property_type_id', 'street_id', 'property_status_id', 'ownership_type_id', 'usage_type_id', 'created_by', 'updated_by'], 'integer'],
            [['description', 'document_url'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['property_name'], 'string', 'max' => 255],
            [['identifier_code'], 'string', 'max' => 200],
            [['uuid'], 'unique'],
            [['identifier_code'], 'unique'],
            [['property_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['property_type_id' => 'id']],
            [['property_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['property_status_id' => 'id']],
            [['ownership_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['ownership_type_id' => 'id']],
            [['usage_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['usage_type_id' => 'id']],
           /* [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
            [['street_id'], 'exist', 'skipOnError' => true, 'targetClass' => Street::class, 'targetAttribute' => ['street_id' => 'street_id']],*/
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
            'property_name' => 'Property Name',
            'property_type_id' => 'Property Type ',
            'description' => 'Description',
            'identifier_code' => 'Identifier Code',
            'street_id' => 'Street (Optional)',
            'property_status_id' => 'Property Status ',
            'document_url' => 'Document Url',
            'ownership_type_id' => 'Ownership Type ',
            'usage_type_id' => 'Usage Type ',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

   public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by = Yii::$app->user->id ?? 1;
        }

        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_by = Yii::$app->user->id ?? 1;

        return true;
    }



        // Relations
    public function getPropertyType()
    {
        return $this->hasOne(ListSource::class, ['id' => 'property_type_id']);
    }
    public function getPropertyStatus(){
        return $this->hasOne(ListSource::class,['id'=>'ownership_type_id']);
    }
 

}
