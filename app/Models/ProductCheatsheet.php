<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCheatsheet extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'file',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fileUrl(): string
    {
        return asset('storage/' . $this->file);
    }

    public function fileExtension(): string
    {
        return strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
    }
}
