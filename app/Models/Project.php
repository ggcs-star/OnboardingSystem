<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Project extends Model
{
    use HasFactory;

    public const STAGES = [
        'documents' => 'Document Template',
        'development' => 'Development',
        'testing' => 'Testing',
        'training' => 'Client Training',
        'live' => 'Live',
    ];

    public const DOCUMENT_STATUSES = ['pending', 'submitted', 'approved', 'rejected'];

    protected $fillable = [
        'product_id',
        'client_id',
        'project_name',
        'expected_live_date',
        'actual_live_date',
        'status',
        'current_stage',
        'progress',
        'assigned_manager',
        'documents',
    ];

    protected $casts = [
        'expected_live_date' => 'date',
        'actual_live_date' => 'date',
        'documents' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_manager');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(ProjectTimeline::class)->orderBy('id');
    }

    public function renewal(): HasOne
    {
        return $this->hasOne(Renewal::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function customizationRequests(): HasMany
    {
        return $this->hasMany(CustomizationRequest::class)->latest();
    }

    /**
     * Flattens the `documents` JSON column into one object per field, each
     * carrying its group_slug/group_label alongside the field's own data.
     */
    public function documentEntries(): Collection
    {
        $entries = collect();

        foreach (($this->documents ?? []) as $groupSlug => $group) {
            foreach (($group['fields'] ?? []) as $fieldKey => $field) {
                $entries->push((object) array_merge($field, [
                    'group_slug' => $groupSlug,
                    'group_label' => $group['label'] ?? $groupSlug,
                    'group_mandatory' => $group['mandatory'] ?? false,
                    'field_key' => $fieldKey,
                ]));
            }
        }

        return $entries;
    }

    /**
     * [count submitted-or-approved, total] document fields for this project.
     */
    public function documentsProgress(): array
    {
        $entries = $this->documentEntries();
        $total = $entries->count();
        $done = $entries->whereIn('status', ['submitted', 'approved'])->count();

        return [$done, $total];
    }

    /**
     * [count submitted-or-approved, total] required fields within groups
     * flagged mandatory-for-onboarding — used to decide whether onboarding
     * can be considered complete.
     */
    public function mandatoryDocumentsProgress(): array
    {
        $entries = $this->documentEntries()->where('group_mandatory', true)->where('required', true);
        $total = $entries->count();
        $done = $entries->whereIn('status', ['submitted', 'approved'])->count();

        return [$done, $total];
    }

    public function isOnboardingComplete(): bool
    {
        [$done, $total] = $this->mandatoryDocumentsProgress();

        if ($done !== $total) {
            return false;
        }

        if ($this->product->subscriptionPlans()->doesntExist()) {
            return true;
        }

        return (bool) $this->renewal?->go_live_date;
    }

    /**
     * Training videos are shared across all of a client's projects for the
     * same product, so this looks up progress on the client, not the project.
     */
    public function trainingProgressFor(ProductTraining $video): ?ClientTrainingProgress
    {
        return $this->client->trainingProgress->firstWhere('training_id', $video->id);
    }

    /**
     * [count completed, total] training videos for this project's product,
     * counted against the client's shared progress (not project-scoped).
     */
    public function trainingProgressCount(): array
    {
        $trainingIds = $this->product->training->pluck('id');
        $total = $trainingIds->count();

        if ($total === 0) {
            return [0, 0];
        }

        $done = $this->client->trainingProgress
            ->whereIn('training_id', $trainingIds)
            ->where('completed', true)
            ->count();

        return [$done, $total];
    }
}
