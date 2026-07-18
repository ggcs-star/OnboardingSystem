<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPolicy extends Model
{
    use HasFactory;

    public const TYPES = [
        'privacy_policy' => 'Privacy Policy',
        'terms_conditions' => 'Terms & Conditions',
        'support_policy' => 'Support Policy',
        'refund_policy' => 'Refund Policy',
    ];

    protected $fillable = [
        'product_id',
        'type',
        'title',
        'content',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
