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

    /**
     * Converts a YouTube/Vimeo watch URL into its embeddable player URL so
     * the video can play inline instead of opening a new tab.
     */
    public function embedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $this->video_url, $matches)) {
            // youtube-nocookie.com avoids the "Video unavailable" failure
            // YouTube's regular embed domain shows when third-party cookies
            // are blocked (e.g. incognito / strict privacy settings).
            return "https://www.youtube-nocookie.com/embed/{$matches[1]}";
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches)) {
            return "https://player.vimeo.com/video/{$matches[1]}";
        }

        return $this->video_url;
    }
}
