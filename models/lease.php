<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class Lease extends ActiveRecord
{
    /**
     * Table name
     */
    public static function tableName()
    {
        return 'lease';
    }

    /**
     * Property for uploaded file
     */
    public $lease_doc_file;

    /**
     * Behaviors: auto timestamps
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class, // handles created_at and updated_at automatically
        ];
    }

    /**
     * Validation rules
     */
    public function rules()
    {
        return [
            [['uuid', 'property_id', 'tenant_id', 'property_price_id', 'status', 'lease_start_date', 'lease_end_date'], 'required'],
            [['property_id', 'tenant_id', 'property_price_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['lease_doc_url'], 'string', 'max' => 255],
            [['uuid'], 'string', 'max' => 100],
            [['lease_start_date', 'lease_end_date'], 'safe'],

            // file upload rule
            [['lease_doc_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx', 'maxSize' => 1024*1024*5],
        ];
    }

    /**
     * Before saving, generate UUID if not set
     */
    public function beforeValidate()
    {
        if (empty($this->uuid)) {
            $this->uuid = Yii::$app->security->generateRandomString(12);
        }
        return parent::beforeValidate();
    }

    /**
     * Upload lease document and save path
     */
    public function uploadDocument()
    {
        if ($this->lease_doc_file) {
            $folder = 'uploads/leases/';
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $fileName = uniqid('lease_') . '.' . $this->lease_doc_file->extension;
            $filePath = $folder . $fileName;
            if ($this->lease_doc_file->saveAs($filePath)) {
                $this->lease_doc_url = $filePath;
                return true;
            }
        }
        return false;
    }

    /**
     * Compute lease duration in months
     */
    public function getDurationMonths()
    {
        if ($this->lease_start_date && $this->lease_end_date) {
            $start = new \DateTime($this->lease_start_date);
            $end = new \DateTime($this->lease_end_date);
            return ($end->format('Y') - $start->format('Y')) * 12 + ($end->format('m') - $start->format('m'));
        }
        return null;
    }

    /**
     * Relations
     */
    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    public function getTenant()
    {
        return $this->hasOne(Tenant::class, ['id' => 'tenant_id']);
    }

    public function getPropertyPrice()
    {
        return $this->hasOne(PropertyPrice::class, ['id' => 'property_price_id']);
    }
}
