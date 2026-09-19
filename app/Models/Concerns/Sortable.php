<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy($this->getKeyName());
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
