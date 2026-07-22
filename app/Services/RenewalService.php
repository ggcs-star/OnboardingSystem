<?php

namespace App\Services;

use App\Models\ProductSubscriptionPlan;
use App\Models\Project;
use App\Models\Renewal;
use App\Models\RenewalHistory;
use Carbon\Carbon;

class RenewalService
{
    /**
     * Legacy path used only by the demo seeder, which advances a project
     * straight to the "live" stage without going through client onboarding.
     */
    public function createForProject(Project $project): Renewal
    {
        $settings = $project->product->renewalSetting;
        $plan = $project->product->subscriptionPlans->first();
        $durationMonths = $plan?->duration_months ?? 12;
        $goLiveDate = Carbon::today();

        return Renewal::updateOrCreate(
            ['project_id' => $project->id],
            [
                'product_subscription_plan_id' => $plan?->id,
                'plan_name' => $plan?->name ?? ($project->product->name . ' Plan'),
                'plan_duration_months' => $durationMonths,
                'go_live_date' => $goLiveDate,
                'expiry_date' => $goLiveDate->copy()->addMonths($durationMonths),
                'renewal_amount' => $plan?->amount,
                'reminder_before_days' => $settings?->reminder_before_days ?? 30,
                'status' => 'active',
            ]
        );
    }

    /**
     * Client's plan choice during onboarding. Doesn't set start/expiry dates
     * yet — those only get set once the whole project is onboarded (see
     * activateIfReady()).
     */
    public function selectPlan(Project $project, ProductSubscriptionPlan $plan): Renewal
    {
        $settings = $project->product->renewalSetting;

        return Renewal::updateOrCreate(
            ['project_id' => $project->id],
            [
                'product_subscription_plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'plan_duration_months' => $plan->duration_months,
                'renewal_amount' => $plan->amount,
                'reminder_before_days' => $settings?->reminder_before_days ?? 30,
                'status' => 'active',
            ]
        );
    }

    public function recordPayment(Renewal $renewal, float $amount, string $date, ?string $mode, ?string $remarks): RenewalHistory
    {
        $history = $renewal->history()->create([
            'payment_date' => $date,
            'amount' => $amount,
            'payment_mode' => $mode,
            'remarks' => $remarks,
        ]);

        $this->activateIfReady($renewal->project);

        return $history;
    }

    /**
     * Sets the subscription's start/expiry dates the moment every onboarding
     * requirement is met: a plan chosen, at least one payment logged (partial
     * is enough), and mandatory documents submitted. Idempotent — does
     * nothing once already activated or until all three are true.
     */
    public function activateIfReady(Project $project): void
    {
        $renewal = $project->renewal()->first();

        if (! $renewal || ! $renewal->product_subscription_plan_id || $renewal->go_live_date) {
            return;
        }

        if (! $renewal->history()->exists()) {
            return;
        }

        [$done, $total] = $project->mandatoryDocumentsProgress();

        if ($done !== $total) {
            return;
        }

        $start = Carbon::today();

        $renewal->update([
            'go_live_date' => $start,
            'expiry_date' => $start->copy()->addMonths($renewal->plan_duration_months),
        ]);
    }

    public function statusFor(?Renewal $renewal): string
    {
        if (! $renewal || ! $renewal->expiry_date) {
            return 'none';
        }

        $daysLeft = Carbon::today()->diffInDays($renewal->expiry_date, false);

        if ($daysLeft < 0) {
            return 'expired';
        }

        if ($daysLeft <= $renewal->reminder_before_days) {
            return 'expiring';
        }

        return 'active';
    }
}
