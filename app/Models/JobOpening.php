<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    use HasSlug, Sortable;

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full-time',
        'part_time' => 'Part-time',
        'contract' => 'Contract',
        'internship' => 'Internship',
    ];

    protected $slugFrom = 'title';

    protected $guarded = ['id'];

    protected $casts = ['is_open' => 'boolean'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_open', true);
    }
}
