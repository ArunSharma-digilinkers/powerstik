<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Fills `slug` from the model's $slugFrom attribute when left empty, keeping it unique.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = $model->slug ?: $model->{$model->slugFrom ?? 'name'};
            $base = Str::slug($source) ?: Str::lower(Str::random(8));
            $slug = $base;

            for ($i = 2; static::where('slug', $slug)->whereKeyNot($model->getKey())->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }

            $model->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
