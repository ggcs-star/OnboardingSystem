<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientProduct extends Model
{
    protected $fillable = [
        'client_id',
        'product_id',
        'status',
        'assigned_by',
        'assigned_at',
        'brand_slots',
    ];

    protected $casts = [
        'assigned_at'=>'datetime',
        'status'=>'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class,'assigned_by');
    }

    public function usedBrandSlots(): int
    {
        return $this->projects()->count();
    }

    public function hasAvailableBrandSlot(): bool
    {
        return $this->usedBrandSlots() < $this->brand_slots;
    }
}