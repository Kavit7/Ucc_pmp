<?php

namespace tests\unit\models;

use app\models\Notification;
use app\models\Users;

/**
 * Covers the notification system built this session: the
 * notifications_enabled opt-out, and the dedupe behavior that
 * syncOverdueBillNotifications() relies on to be safely re-run on every
 * page load without spamming duplicate rows.
 */
class NotificationTest extends \Codeception\Test\Unit
{
    private function makeUser(bool $notificationsEnabled = true)
    {
        $user = new Users([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => 'Notif Test User',
            'email' => 'notif-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
            'notifications_enabled' => $notificationsEnabled ? 1 : 0,
        ]);
        $user->save(false);
        return $user;
    }

    public function testNotifyCreatesARow()
    {
        $user = $this->makeUser();

        $result = Notification::notify($user->user_id, 'Test title', 'Test message');

        verify($result)->true();
        $row = Notification::find()->where(['user_id' => $user->user_id, 'title' => 'Test title'])->one();
        verify($row)->notNull();
        verify($row->is_read)->equals(0);
    }

    public function testNotifyRespectsDisabledPreference()
    {
        $user = $this->makeUser(false);

        $result = Notification::notify($user->user_id, 'Should not appear');

        verify($result)->false();
        $count = Notification::find()->where(['user_id' => $user->user_id])->count();
        verify($count)->equals(0);
    }

    public function testDuplicateRelatedNotificationIsSkipped()
    {
        $user = $this->makeUser();

        $first = Notification::notify($user->user_id, 'Overdue bill', 'msg', null, 'bill_overdue', 999);
        $second = Notification::notify($user->user_id, 'Overdue bill', 'msg again', null, 'bill_overdue', 999);

        verify($first)->true();
        verify($second)->false();

        $count = Notification::find()->where(['user_id' => $user->user_id, 'related_type' => 'bill_overdue', 'related_id' => 999])->count();
        verify($count)->equals(1);
    }

    public function testMarkReadUpdatesFlag()
    {
        $user = $this->makeUser();
        Notification::notify($user->user_id, 'To be read');
        $notif = Notification::find()->where(['user_id' => $user->user_id])->one();

        verify($notif->is_read)->equals(0);
        $notif->markRead();
        verify($notif->is_read)->equals(1);
    }
}
