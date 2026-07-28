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
}