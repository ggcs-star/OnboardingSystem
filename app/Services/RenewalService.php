<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Renewal;
use Carbon\Carbon;

class RenewalService
{
    public function createForProject(Project $project): Renewal
    {
        $settings = $project->product->renewalSetting;
        $durationMonths = $settings?->default_plan_duration_months ?? 12;
        $goLiveDate = Carbon::today();

        return Renewal::updateOrCreate(
            ['project_id' => $project->id],
            [
                'plan_name' => $project->product->name . ' Plan',
                'plan_duration_months' => $durationMonths,
                'go_live_date' => $goLiveDate,
                'expiry_date' => $goLiveDate->copy()->addMonths($durationMonths),
                'renewal_amount' => $settings?->default_renewal_amount,
                'reminder_before_days' => $settings?->reminder_before_days ?? 30,
                'status' => 'active',
            ]
        );
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
