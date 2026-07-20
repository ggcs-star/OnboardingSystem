<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'owner_name',
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

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function trainingProgress(): HasMany
    {
        return $this->hasMany(ClientTrainingProgress::class);
    }
}
