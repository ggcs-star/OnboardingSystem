<?php

namespace App\Traits;

trait HasSortOrder
{
    protected static function bootHasSortOrder(): void
    {
        static::creating(function ($model) {
            if (empty($model->sort_order)) {
                $model->sort_order = static::where($model->sortOrderScopeColumn(), $model->{$model->sortOrderScopeColumn()})
                    ->max('sort_order') + 1;
            }
        });
    }

    public function sortOrderScopeColumn(): string
    {
        return 'product_id';
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
