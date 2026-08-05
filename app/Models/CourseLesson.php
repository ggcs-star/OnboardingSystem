<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'course_module_id',
        'title',
        'description',
        'video_source',
        'video_path',
        'video_url',
        'duration',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'course_module_id';
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(CourseQuizCheckpoint::class)->orderBy('timestamp_seconds');
    }

    public function fileUrl(): ?string
    {
        return $this->video_path ? asset('storage/' . $this->video_path) : null;
    }

    /**
     * Bare 11-char YouTube video ID (for the IFrame Player API), not an
     * embed URL — the client player instantiates YT.Player with this.
     */
    public function youtubeVideoId(): ?string
    {
        if ($this->video_source !== 'youtube' || ! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
