<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['last_hit_at' => 'datetime'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('from_path');
    }

    /** Stored as a leading-slash path without query string or trailing slash. */
    public static function normalizePath(string $path): string
    {
        $path = '/'.trim(parse_url($path, PHP_URL_PATH) ?? '', '/');

        return strtolower($path);
    }
}
