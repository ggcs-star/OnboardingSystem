<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTraining extends Model
{
    use HasFactory, HasSortOrder;

    protected $table = 'product_training';

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'video_url',
        'duration',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
