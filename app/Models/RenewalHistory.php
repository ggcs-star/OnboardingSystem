<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenewalHistory extends Model
{
    use HasFactory;

    protected $table = 'renewal_history';

    protected $fillable = [
        'renewal_id',
        'invoice_no',
        'payment_date',
        'amount',
        'payment_mode',
        'plan_duration_months',
        'start_date',
        'expiry_date',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function renewal(): BelongsTo
    {
        return $this->belongsTo(Renewal::class);
    }
}
