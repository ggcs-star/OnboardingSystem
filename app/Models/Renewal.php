<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Renewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'product_subscription_plan_id',
        'plan_name',
        'plan_duration_months',
        'go_live_date',
        'expiry_date',
        'renewal_amount',
        'reminder_before_days',
        'status',
        'renewed_at',
        'remarks',
    ];

    protected $casts = [
        'go_live_date' => 'date',
        'expiry_date' => 'date',
        'renewed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProductSubscriptionPlan::class, 'product_subscription_plan_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(RenewalHistory::class)->latest('payment_date');
    }

    public function amountPaid(): float
    {
        return (float) $this->history->sum('amount');
    }

    public function amountDue(): float
    {
        return max(0, (float) $this->renewal_amount - $this->amountPaid());
    }
}
