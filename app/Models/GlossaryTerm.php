<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GlossaryTerm extends Model
{
    use HasSlug;

    protected $slugFrom = 'term';

    protected $guarded = ['id'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('term');
    }
}
