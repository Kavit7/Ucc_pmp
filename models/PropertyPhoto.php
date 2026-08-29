<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class PropertyPhoto extends ActiveRecord
{
    public $photoFile;

    public static function tableName()
    {
        return 'property_photo';
    }

    public function rules()
    {
        return [
            [['property_id'], 'required'],
            [['property_id', 'created_by'], 'integer'],
            [['photo_url'], 'string', 'max' => 255],
            [['photoFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert && empty($this->uuid)) {
            $lastUuid = self::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
            $this->uuid = $lastUuid ? 'PPh_' . ((int) str_replace('PPh_', '', $lastUuid) + 1) : 'PPh_1';
        }

        return true;
    }

    /**
     * Validates and saves the uploaded file, then persists the record.
     * Returns false (with validation errors on $this) without touching
     * disk if the file is missing/invalid.
     */
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }

        $uploadDir = Yii::getAlias('@webroot/uploads/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = Yii::$app->security->generateRandomString() . '.' . $this->photoFile->extension;
        if (!$this->photoFile->saveAs($uploadDir . $fileName)) {
            return false;
        }

        $this->photo_url = 'uploads/' . $fileName;
        return $this->save(false);
    }

    public function beforeDelete()
    {
        if ($this->photo_url) {
            $fullPath = Yii::getAlias('@webroot/' . $this->photo_url);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
        return parent::beforeDelete();
    }
}
