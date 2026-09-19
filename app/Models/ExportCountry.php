<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExportCountry extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean', 'lat' => 'float', 'lng' => 'float'];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
