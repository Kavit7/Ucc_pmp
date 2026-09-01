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
            [['lease_id', 'bill_status', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['due_date', 'paid_date', 'created_at', 'updated_at'], 'safe'],
            [['bill_status'], 'exist', 'skipOnError' => true, 'targetClass' => ListSource::class, 'targetAttribute' => ['bill_status' => 'id']],
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

    /**
     * Creates and saves a "Pending" bill for a lease. Used both for the
     * initial bill at lease creation/renewal and for each subsequent
     * recurring bill, so the uuid-gen + status-lookup sequence lives in
     * exactly one place instead of being copy-pasted per call site.
     *
     * @param int $leaseId
     * @param float $amount
     * @param string $dueDate Y-m-d
     * @param int|null $createdBy
     * @return static|null the saved bill, or null if it failed to save
     */
    public static function createPending($leaseId, $amount, $dueDate, $createdBy = null)
    {
        $pendingStatusId = ListSource::find()
            ->where(['list_Name' => 'Pending', 'category' => 'Bill Status'])
            ->select('id')
            ->scalar();

        $bill = new self();
        $bill->uuid = Yii::$app->security->generateRandomString(12);
        $bill->lease_id = $leaseId;
        $bill->amount = $amount;
        $bill->due_date = $dueDate;
        $bill->bill_status = $pendingStatusId ?: null;
        $bill->created_by = $createdBy;

        return $bill->save(false) ? $bill : null;
    }
}