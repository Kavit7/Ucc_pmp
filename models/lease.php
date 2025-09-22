<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class Lease extends ActiveRecord
{
    public $lease_doc_file;

    public static function tableName()
    {
        return 'lease';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['property_id', 'tenant_id', 'property_price_id', 'status', 'lease_start_date', 'lease_end_date'], 'required'],
            [['property_id', 'tenant_id', 'property_price_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['lease_start_date', 'lease_end_date'], 'date', 'format' => 'php:Y-m-d'],
            [['lease_doc_url'], 'string', 'max' => 255],
            [['uuid'], 'string', 'max' => 100],
            [['lease_doc_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx', 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'uuid' => 'UUID',
            'property_id' => 'Property',
            'tenant_id' => 'Tenant',
            'property_price_id' => 'Price',
            'lease_doc_file' => 'Lease Document',
            'lease_doc_url' => 'Document URL',
            'status' => 'Status',
            'lease_start_date' => 'Lease Start Date',
            'lease_end_date' => 'Lease End Date',
            'duration_months' => 'Duration (Months)',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /*public function beforeValidate()
    {
        if (empty($this->uuid)) {
            $this->uuid = Yii::$app->security->generateRandomString(12);
        }

        if (Yii::$app->user && !Yii::$app->user->isGuest) {
            $userId =2;
            if ($this->isNewRecord) {
                $this->created_by = 2;
            }
            $this->updated_by = 2;
        }

        return parent::beforeValidate();
    }*/
 public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            // Set the user who created the record
            $this->created_by = Yii::$app->user->id;
            $this->updated_by=Yii::$app->user->id;
            // Generate UUID if empty
           if (empty($this->uuid)) {
    $lastNumber = (int) self::find()
        ->select(['MAX(CAST(SUBSTRING(uuid,6) AS UNSIGNED)) AS maxNumber'])
        ->where(['like', 'uuid', 'Lease_%', false])
        ->scalar();

    $lastNumber = $lastNumber ?: 0;

    do {
        $lastNumber++;
        $newUuid = 'Lease_' . $lastNumber;
    } while (self::find()->where(['uuid' => $newUuid])->exists());

    $this->uuid = $newUuid;
}

        
            $this->created_at = date('Y-m-d H:i:s');
        }

        $this->updated_at = date('Y-m-d H:i:s');

        return true;
    }

    public function uploadDocument()
    {
        if ($this->lease_doc_file instanceof UploadedFile) {
            $folder = Yii::getAlias('@webroot/uploads/leases/');
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $fileName = uniqid('lease_') . '.' . $this->lease_doc_file->extension;
            $filePath = $folder . $fileName;

            if ($this->lease_doc_file->saveAs($filePath)) {
                $this->lease_doc_url = 'uploads/leases/' . $fileName;
                return true;
            }
            return false;
        }
        return true;
    }

    public function getDurationMonths()
    {
        if ($this->lease_start_date && $this->lease_end_date) {
            $start = new \DateTime($this->lease_start_date);
            $end = new \DateTime($this->lease_end_date);

            $months = ($end->format('Y') - $start->format('Y')) * 12;
            $months += $end->format('m') - $start->format('m');

            if ($end->format('d') >= $start->format('d')) {
                $months += 1;
            }

            return max($months, 1);
        }

        return 1;
    }

    public function getProperty()
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    public function getTenant()
    {
        return $this->hasOne(Users::class, ['user_id' => 'tenant_id']);
    }

    public function getPropertyPrice()
    {
        return $this->hasOne(PropertyPrice::class, ['id' => 'property_price_id']);
    }

    /**
     * Status relation to ListSource
     */
    public function getStatusLabel()
    {
        return $this->hasOne(\app\models\ListSource::class, ['id' => 'status']);
    }

    public function beforeDelete()
    {
        if ($this->lease_doc_url && file_exists(Yii::getAlias('@webroot/' . $this->lease_doc_url))) {
            @unlink(Yii::getAlias('@webroot/' . $this->lease_doc_url));
        }
        return parent::beforeDelete();
    }
}