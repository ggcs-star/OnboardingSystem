<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductDocumentGroup extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'is_mandatory',
        'sort_order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ProductDocumentField::class, 'product_document_group_id')->ordered();
    }
}
