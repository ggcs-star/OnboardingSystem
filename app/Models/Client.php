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

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
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

    public function courseLessonProgress(): HasMany
    {
        return $this->hasMany(ClientCourseLessonProgress::class);
    }

    public function quizAnswers(): HasMany
    {
        return $this->hasMany(ClientQuizAnswer::class);
    }

public function projects(): HasMany
{
    return $this->hasMany(Project::class);
}
    public function clientProducts()
    {
        return $this->hasMany(ClientProduct::class);
    }

    public function lmsProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            LmsProduct::class,
            'lms_client_products',
            'client_id',
            'lms_product_id'
        )->withPivot([
                    'status',
                    'assigned_by',
                    'assigned_at',
                ])->withTimestamps();
    }

    public function lmsClientProducts(): HasMany
    {
        return $this->hasMany(LmsClientProduct::class);
    }

    public function inquiries()
{
    return $this->hasMany(ProductInquiry::class);
}

    /**
     * Document progress across every assigned product. Products the client
     * hasn't started onboarding yet (no Project created) count as fully
     * pending, using the product's current document field count. A project
     * can also exist for a product no longer in the client_products pivot,
     * so both sources are unioned.
     */
    public function documentsSummary(): object
    {
        $this->loadMissing(['products.documentGroups.fields', 'projects.product.documentGroups.fields']);

        $projectsByProduct = $this->projects->groupBy('product_id');
        $productsById = $this->products->keyBy('id');

        $productIds = $productsById->keys()->merge($projectsByProduct->keys())->unique();

        $done = 0;
        $total = 0;
        $breakdown = [];

        foreach ($productIds as $productId) {
            $projects = $projectsByProduct->get($productId, collect());

            if ($projects->isEmpty()) {
                $product = $productsById->get($productId);
                $fieldsTotal = $product?->documentGroups->sum(fn ($group) => $group->fields->count()) ?? 0;

                $total += $fieldsTotal;

                if ($fieldsTotal > 0) {
                    $breakdown[] = ['product' => $product->name, 'brand' => null, 'pending' => $fieldsTotal, 'total' => $fieldsTotal];
                }

                continue;
            }

            foreach ($projects as $project) {
                [$projectDone, $projectTotal] = $project->documentsProgress();

                $done += $projectDone;
                $total += $projectTotal;

                $pending = $projectTotal - $projectDone;
                if ($pending > 0) {
                    $breakdown[] = [
                        'product' => $project->product?->name,
                        'brand' => $project->brand_name ?? $project->project_name,
                        'pending' => $pending,
                        'total' => $projectTotal,
                    ];
                }
            }
        }

        return (object) [
            'done' => $done,
            'total' => $total,
            'pending' => $total - $done,
            'breakdown' => $breakdown,
        ];
    }
}

