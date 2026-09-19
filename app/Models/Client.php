<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $casts = ['show_logo' => 'boolean'];

    public function exportCountry(): BelongsTo
    {
        return $this->belongsTo(ExportCountry::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** Clients with permission to be shown publicly. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('show_logo', true);
    }
}
