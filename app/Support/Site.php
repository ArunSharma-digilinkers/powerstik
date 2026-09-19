<?php

namespace App\Support;

use App\Models\ExportCountry;
use App\Models\Setting;
use Illuminate\Support\Collection;

/** Small helpers the public layout needs on every page. */
class Site
{
    public static function setting(string $key, ?string $default = null): ?string
    {
        $value = Setting::get($key);

        return filled($value) ? $value : $default;
    }

    /** wa.me link with the pre-filled message, or null when no number is set yet. */
    public static function whatsappUrl(): ?string
    {
        $number = preg_replace('/\D/', '', (string) static::setting('whatsapp'));

        if ($number === '') {
            return null;
        }

        $message = static::setting('whatsapp_message');

        return 'https://wa.me/'.$number.($message ? '?text='.rawurlencode($message) : '');
    }

    /** "+91 98100 00000" → "tel:+919810000000" */
    public static function telUrl(?string $phone): ?string
    {
        $digits = preg_replace('/[^\d+]/', '', (string) $phone);

        return $digits === '' ? null : 'tel:'.$digits;
    }

    /** @return Collection<int, ExportCountry> */
    public static function exportCountries(): Collection
    {
        return once(fn () => ExportCountry::published()->ordered()->get());
    }
}
