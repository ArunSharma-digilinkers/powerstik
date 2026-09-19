<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings (phone, WhatsApp number, counters, social links…).
 * The editable keys and their defaults are declared in config/settings.php.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings.all';

    /** @return array<string, string|null> */
    public static function allValues(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $defaults = collect(config('settings'))
                ->flatMap(fn ($group) => collect($group['fields'])->map(fn ($f) => $f['default'] ?? null))
                ->all();

            return array_merge($defaults, static::query()->pluck('value', 'key')->all());
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allValues()[$key] ?? $default;
    }

    /** @param array<string, string|null> $values */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
