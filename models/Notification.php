<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;
use yii\helpers\Url;

class Notification extends ActiveRecord
{
    public static function tableName()
    {
        return 'notification';
    }

    public function rules()
    {
        return [
            [['uuid', 'user_id', 'title'], 'required'],
            [['user_id', 'related_id'], 'integer'],
            [['message'], 'string'],
            [['is_read'], 'boolean'],
            [['created_at'], 'safe'],
            [['uuid'], 'string', 'max' => 100],
            [['title', 'link'], 'string', 'max' => 255],
            [['related_type'], 'string', 'max' => 50],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

    public function markRead()
    {
        $this->is_read = 1;
        return $this->save(false, ['is_read']);
    }

    /**
     * Creates a notification for one user, respecting their
     * notifications_enabled preference. When related_type/related_id are
     * given, duplicate notifications for the same user+event are silently
     * skipped (enforced by a unique index), which is what makes it safe to
     * call syncOverdueBillNotifications() on every page load.
     */
    public static function notify($userId, $title, $message = null, $link = null, $relatedType = null, $relatedId = null)
    {
        $user = Users::findOne($userId);
        if (!$user || !$user->notifications_enabled) {
            return false;
        }

        $notification = new self([
            'uuid' => Yii::$app->security->generateRandomString(20),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link ? Url::to($link) : null,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);

        try {
            return $notification->save();
        } catch (DbException $e) {
            // Duplicate (user_id, related_type, related_id) - already notified, ignore.
            return false;
        }
    }

    /**
     * Notifies every user holding one of the given roles.
     */
    public static function notifyRoles(array $roles, $title, $message = null, $link = null, $relatedType = null, $relatedId = null)
    {
        $userIds = Users::find()->select('user_id')->where(['role' => $roles])->column();
        foreach ($userIds as $userId) {
            self::notify($userId, $title, $message, $link, $relatedType, $relatedId);
        }
    }

    /**
     * Lazily materializes notifications for bills that are pending and past
     * due, for both the tenant and staff (admin/manager). Safe to call on
     * every request: duplicates are skipped via the unique index on
     * (user_id, related_type, related_id).
     */
    public static function syncOverdueBillNotifications()
    {
        $pendingId = ListSource::find()
            ->where(['list_Name' => 'Pending'])
            ->andWhere(['parent_id' => ListSource::find()->select('id')->where(['list_Name' => 'Bill Status'])])
            ->select('id')
            ->scalar();

        if (!$pendingId) {
            return;
        }

        $overdueBills = Bill::find()
            ->with('lease')
            ->where(['bill_status' => $pendingId])
            ->andWhere(['<', 'due_date', date('Y-m-d')])
            ->all();

        foreach ($overdueBills as $bill) {
            $property = $bill->lease->property->property_name ?? 'a property';
            $amount = number_format($bill->amount, 2);

            if ($bill->lease && $bill->lease->tenant_id) {
                self::notify(
                    $bill->lease->tenant_id,
                    'Overdue bill',
                    "Your bill for {$property} (TZS {$amount}) was due on {$bill->due_date} and is still unpaid.",
                    ['report/revenue'],
                    'bill_overdue',
                    $bill->id
                );
            }

            self::notifyRoles(
                ['admin', 'manager'],
                'Overdue bill',
                "Bill for {$property} (TZS {$amount}, due {$bill->due_date}) is overdue.",
                ['report/revenue'],
                'bill_overdue_staff',
                $bill->id
            );
        }
    }
}
