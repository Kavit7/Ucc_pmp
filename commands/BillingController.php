<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Lease;
use app\models\Bill;
use app\models\ListSource;
use app\models\Notification;

/**
 * Recurring bill generation. Intended to be run daily via a scheduled task
 * (cron / Windows Task Scheduler):
 *   php yii billing/generate-recurring
 *
 * Overdue reminders are handled separately and don't need scheduling -
 * Notification::syncOverdueBillNotifications() already runs on every page
 * load (see views/layouts/custom.php) and is duplicate-safe.
 */
class BillingController extends Controller
{
    /**
     * For every active lease whose most recent bill's due date has already
     * passed, generates the next month's bill (one rent period at a time)
     * if one doesn't already exist for that period. This only covers
     * ongoing recurring bills - the first bill for a lease is still
     * created at lease creation/renewal time, as before.
     */
    public function actionGenerateRecurring()
    {
        $activeStatusId = ListSource::find()
            ->where(['list_Name' => 'Active', 'category' => 'Lease Status'])
            ->select('id')->scalar();

        if (!$activeStatusId) {
            $this->stdout("No 'Active' lease status found - nothing to do.\n");
            return;
        }

        $pendingStatusId = ListSource::find()
            ->where(['list_Name' => 'Pending', 'category' => 'Bill Status'])
            ->select('id')->scalar();

        $today = date('Y-m-d');
        $created = 0;

        $leases = Lease::find()
            ->where(['status' => $activeStatusId])
            ->andWhere(['>=', 'lease_end_date', $today])
            ->with(['propertyPrice'])
            ->all();

        foreach ($leases as $lease) {
            $latestBill = Bill::find()
                ->where(['lease_id' => $lease->id])
                ->orderBy(['due_date' => SORT_DESC])
                ->one();

            if (!$latestBill) {
                // No bill at all yet - lease creation/renewal is responsible
                // for the first one, so skip it here.
                continue;
            }

            if ($latestBill->due_date >= $today) {
                // Current period's bill already exists and isn't due yet.
                continue;
            }

            $nextDueDate = date('Y-m-d', strtotime($latestBill->due_date . ' +1 month'));
            if ($nextDueDate > $lease->lease_end_date) {
                // Would fall past the lease end - let renewal handle it.
                continue;
            }

            $amount = $lease->propertyPrice->unit_amount ?? 0;
            if ($amount <= 0) {
                continue;
            }

            $bill = new Bill();
            $bill->uuid = Yii::$app->security->generateRandomString(12);
            $bill->lease_id = $lease->id;
            $bill->amount = $amount;
            $bill->due_date = $nextDueDate;
            $bill->bill_status = $pendingStatusId;
            $bill->save(false);

            $propertyName = $lease->property->property_name ?? 'your property';
            Notification::notify(
                $lease->tenant_id,
                'New bill generated',
                "A new bill of TZS " . number_format($amount, 2) . " for {$propertyName} is due on {$nextDueDate}.",
                ['custom/bill']
            );

            $created++;
            $this->stdout("Lease #{$lease->id}: generated bill due {$nextDueDate} (TZS " . number_format($amount, 2) . ")\n");
        }

        $this->stdout("Done. {$created} bill(s) generated.\n");
    }
}
