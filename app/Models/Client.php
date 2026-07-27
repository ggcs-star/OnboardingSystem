<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'owner_name',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'logo',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'client_products',
            'client_id',
            'product_id'
        )->withPivot([
                    'status',
                    'assigned_by',
                    'assigned_at',
                ])->withTimestamps();
    }

    public function trainingProgress(): HasMany
    {
        return $this->hasMany(ClientTrainingProgress::class);
    }

public function projects(): HasMany
{
    return $this->hasMany(Project::class);
}
    public function clientProducts()
    {
        return $this->hasMany(ClientProduct::class);
    }

    public function inquiries()
{
    return $this->hasMany(ProductInquiry::class);
}
}

