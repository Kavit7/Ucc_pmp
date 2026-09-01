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
     * For every active lease whose billed coverage has run out before its
     * lease_end_date, generates the next month's bill (one rent period at
     * a time). The first bill for a lease (created at lease creation or
     * renewal) already covers the lease's full `duration_months` upfront,
     * so "coverage end" is NOT simply the latest bill's due_date - it's
     * lease_start_date plus every month billed so far (duration_months
     * from the first bill, +1 for each recurring bill generated since).
     * Getting this wrong previously caused real over-billing: a lease's
     * first bill already covered its whole term, but the old due_date-only
     * check treated that bill as "expired" the day after it was due and
     * generated an extra bill on top of the fully-paid term.
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

        $today = date('Y-m-d');
        $created = 0;

        $leases = Lease::find()
            ->where(['status' => $activeStatusId])
            ->andWhere(['>=', 'lease_end_date', $today])
            ->with(['propertyPrice'])
            ->all();

        foreach ($leases as $lease) {
            $billCount = Bill::find()->where(['lease_id' => $lease->id])->count();

            if ($billCount == 0) {
                // No bill at all yet - lease creation/renewal is responsible
                // for the first one, so skip it here.
                continue;
            }

            // First bill already covers duration_months; every bill after
            // that covers exactly one more month.
            $monthsCovered = ($lease->duration_months ?: 1) + ((int) $billCount - 1);
            $coverageEnd = date('Y-m-d', strtotime($lease->lease_start_date . " +{$monthsCovered} months"));

            if ($coverageEnd > $today) {
                // Already billed through today - nothing to do yet.
                continue;
            }

            if ($coverageEnd >= $lease->lease_end_date) {
                // Would fall at/past the lease end - let renewal handle it.
                continue;
            }

            $amount = $lease->propertyPrice->unit_amount ?? 0;
            if ($amount <= 0) {
                continue;
            }

            $bill = Bill::createPending($lease->id, $amount, $coverageEnd, null);
            if (!$bill) {
                continue;
            }

            $propertyName = $lease->property->property_name ?? 'your property';
            Notification::notify(
                $lease->tenant_id,
                'New bill generated',
                "A new bill of TZS " . number_format($amount, 2) . " for {$propertyName} is due on {$coverageEnd}.",
                ['custom/bill']
            );

            $created++;
            $this->stdout("Lease #{$lease->id}: generated bill due {$coverageEnd} (TZS " . number_format($amount, 2) . ")\n");
        }

        $this->stdout("Done. {$created} bill(s) generated.\n");
    }
}
