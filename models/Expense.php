<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\components\AuditLogBehavior;
use app\components\Ledger;

/**
 * A property expense (repairs, utilities, taxes, etc.), assumed paid in
 * cash/bank immediately on entry - there's no accrual/"unpaid expense"
 * concept here, matching how payments are recorded elsewhere in the app.
 */
class Expense extends ActiveRecord
{
    public static function tableName()
    {
        return 'expense';
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
            [['property_id', 'amount', 'expense_type'], 'required'],
            [['property_id', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string'],
            [['expense_type'], 'string', 'max' => 255],
            [['document_url'], 'string'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::class, 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'property_id' => 'Property',
            'amount' => 'Amount (TZS)',
            'expense_type' => 'Type',
            'description' => 'Description',
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
        if ($insert) {
            $this->uuid = Yii::$app->security->generateRandomString(12);
            $this->created_by = Yii::$app->user->id ?? null;
        }
        $this->updated_by = Yii::$app->user->id ?? null;
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $propertyName = $this->property->property_name ?? "property #{$this->property_id}";
            Ledger::postSafely(
                date('Y-m-d'),
                "{$this->expense_type}: {$propertyName}",
                [
                    ['account' => '5000', 'debit' => $this->amount],
                    ['account' => '1000', 'credit' => $this->amount],
                ],
                'expense',
                $this->id
            );
        }
    }
}
