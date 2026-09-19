<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Industry extends Model
{
    use HasSlug, Sortable;

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
