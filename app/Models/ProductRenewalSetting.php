<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRenewalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'default_plan_duration_months',
        'default_renewal_amount',
        'reminder_before_days',
        'auto_renew_reminder',
    ];

    protected $casts = [
        'auto_renew_reminder' => 'boolean',
        'default_renewal_amount' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
