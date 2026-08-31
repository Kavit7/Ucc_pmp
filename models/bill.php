<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\components\AuditLogBehavior;

class Bill extends ActiveRecord
{
    public static function tableName()
    {
        return 'bill';
    }

    public function behaviors()
    {
        return [
            'audit' => ['class' => AuditLogBehavior::class],
        ];
    }

    public function rules()
    {
        return [
            [['uuid', 'lease_id', 'amount', 'due_date'], 'required'],
            [['lease_id'], 'integer'],
            [['amount'], 'number'],
            [['due_date', 'paid_date', 'created_at', 'updated_at'], 'safe'],
            [['bill_status'], 'in', 'range' => ['pending','paid','overdue']],
            [['uuid'], 'string', 'max' => 100],
            [['receipt_url'], 'string'],
        ];
    }

    public function getLease()
    {
        return $this->hasOne(\app\models\Lease::class, ['id' => 'lease_id']);
    }
    public function getBillStatus(){
        return $this->hasOne(ListSource::class,['id'=>'bill_status']);
    }
}