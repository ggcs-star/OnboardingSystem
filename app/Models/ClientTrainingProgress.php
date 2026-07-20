<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTrainingProgress extends Model
{
    use HasFactory;

    protected $table = 'client_training_progress';

    protected $fillable = [
        'client_id',
        'training_id',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(ProductTraining::class, 'training_id');
    }
}
