<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

class Property extends \yii\db\ActiveRecord
{
    // Add a separate variable for file upload
    public $documentFile;

    public static function tableName()
    {
        return 'property';
    }

    public function rules()
    {
        return [
            [['description', 'identifier_code', 'street_id', 'document_url', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['uuid', 'property_name', 'property_type_id', 'property_status_id', 'ownership_type_id', 'usage_type_id', 'identifier_code'], 'required'],
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
            [['documentFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, pdf'],
        ];
    }

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
            $this->created_by = Yii::$app->user->id ?? 15;
        }

        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_by = Yii::$app->user->id ?? 15;

        return true;
    }

    // Relations
    public function getPropertyType()
    {
        return $this->hasOne(ListSource::class, ['id' => 'property_type_id']);
    }
  public function getPropertyStatus(){
    return $this->hasOne(ListSource::class,['id'=>'property_status_id']);
  }
    public function getPropertyOwnerShip()
    {
        return $this->hasOne(ListSource::class, ['id' => 'ownership_type_id']);
    }
 public function getPropertyPrice(){
    return $this->hasMany(PropertyPrice::class,['property_id'=>'id']);
 }
    public function upload()
    {
        if ($this->validate()) {
            $path = 'uploads/' . $this->documentFile->baseName . '.' . $this->documentFile->extension;
            $this->documentFile->saveAs($path);
            $this->document_url = $path; // save path string into DB
            return true;
        }
        return false;
    }
     public function getUsageType(){
        return $this->hasOne(ListSource::class,['id'=>'usage_type_id']);
    }
    public function getStreet(){
        return $this->hasOne(Street::class,['street_id'=>'street_id']);
    }
}
