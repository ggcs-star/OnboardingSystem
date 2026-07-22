<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDocumentField extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'product_id',
        'product_document_group_id',
        'label',
        'field_key',
        'field_type',
        'required',
        'placeholder',
        'options',
        'validation',
        'sort_order',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductDocumentGroup::class, 'product_document_group_id');
    }
}
