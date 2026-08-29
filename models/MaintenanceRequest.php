<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class MaintenanceRequest extends ActiveRecord
{
    public $photoFile;

    public static function tableName()
    {
        return 'maintenance_request';
    }

    public function rules()
    {
        return [
            [['property_id', 'title', 'status_id'], 'required'],
            [['property_id', 'lease_id', 'reported_by', 'assigned_to', 'priority_id', 'status_id'], 'integer'],
            [['description'], 'string'],
            [['title', 'photo_url'], 'string', 'max' => 255],
            [['uuid'], 'string', 'max' => 100],
            [['resolved_at', 'created_at', 'updated_at'], 'safe'],
            [['photoFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 3 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Issue',
            'description' => 'Description',
            'property_id' => 'Property',
            'priority_id' => 'Priority',
            'status_id' => 'Status',
            'assigned_to' => 'Assigned To',
            'photoFile' => 'Photo',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            if (empty($this->uuid)) {
                $lastUuid = self::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                $this->uuid = $lastUuid ? 'MR_' . ((int) str_replace('MR_', '', $lastUuid) + 1) : 'MR_1';
            }
            if (empty($this->status_id)) {
                $this->status_id = self::openStatusId();
            }
        }

        return true;
    }

    public function uploadPhoto()
    {
        if (!$this->photoFile instanceof UploadedFile) {
            return true;
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg'];
        if (!in_array(strtolower($this->photoFile->extension), $allowedExtensions, true)) {
            $this->addError('photoFile', 'Photo must be a png, jpg, or jpeg file.');
            return false;
        }

        $folder = Yii::getAlias('@webroot/uploads/');
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = Yii::$app->security->generateRandomString() . '.' . $this->photoFile->extension;
        if (!$this->photoFile->saveAs($folder . $fileName)) {
            return false;
        }

        $this->photo_url = 'uploads/' . $fileName;
        return $this->save(false, ['photo_url']);
    }

    public static function openStatusId()
    {
        return ListSource::find()
            ->where(['list_Name' => 'Open', 'parent_id' => ListSource::find()->select('id')->where(['list_Name' => 'Maintenance Status'])])
            ->select('id')
            ->scalar();
    }

    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    public function getLease()
    {
        return $this->hasOne(Lease::class, ['id' => 'lease_id']);
    }

    public function getReportedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'reported_by']);
    }

    public function getAssignedTo()
    {
        return $this->hasOne(Users::class, ['user_id' => 'assigned_to']);
    }

    public function getPriority()
    {
        return $this->hasOne(ListSource::class, ['id' => 'priority_id']);
    }

    public function getStatus()
    {
        return $this->hasOne(ListSource::class, ['id' => 'status_id']);
    }
}
