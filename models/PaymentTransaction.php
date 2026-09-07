<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PaymentTransaction extends ActiveRecord
{
    public static function tableName()
    {
        return 'payment_transaction';
    }

    public function rules()
    {
        return [
            [['bill_id', 'tenant_id', 'tx_ref', 'amount'], 'required'],
            [['bill_id', 'tenant_id'], 'integer'],
            [['amount'], 'number'],
            [['tx_ref', 'flw_transaction_id'], 'string', 'max' => 100],
            [['currency'], 'string', 'max' => 10],
            [['status'], 'in', 'range' => ['pending', 'successful', 'failed']],
            [['gateway_response'], 'string'],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && empty($this->uuid)) {
            $this->uuid = Yii::$app->security->generateRandomString(16);
        }
        return true;
    }

    public function getBill()
    {
        return $this->hasOne(Bill::class, ['id' => 'bill_id']);
    }

    public function getTenant()
    {
        return $this->hasOne(Users::class, ['user_id' => 'tenant_id']);
    }
}
