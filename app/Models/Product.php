<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tagline',
        'category',
        'introduction',
        'image',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function documentFields(): HasMany
    {
        return $this->hasMany(ProductDocumentField::class)->ordered();
    }

    public function documentGroups(): HasMany
    {
        return $this->hasMany(ProductDocumentGroup::class)->ordered()->with('fields');
    }

    public function training(): HasMany
    {
        return $this->hasMany(ProductTraining::class)->ordered();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class)->ordered();
    }

    public function lmsProduct(): HasOne
    {
        return $this->hasOne(LmsProduct::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class)->orderBy('id');
    }

    public function policies(): HasMany
    {
        return $this->hasMany(ProductPolicy::class);
    }

    public function renewalSetting(): HasOne
    {
        return $this->hasOne(ProductRenewalSetting::class);
    }

    public function subscriptionPlans(): HasMany
    {
        return $this->hasMany(ProductSubscriptionPlan::class)->ordered();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function projectsCount(): int
    {
        return $this->projects()->count();
    }

    public function clientsCount(): int
    {
        return $this->projects()->distinct('client_id')->count('client_id');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function clients()
{
    return $this->belongsToMany(
        Client::class,
        'client_products'
    )
    ->withPivot([
        'status',
        'assigned_by',
        'assigned_at'
    ])
    ->withTimestamps();
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
