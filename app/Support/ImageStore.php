<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Saves uploaded images to the public_uploads disk as WebP, plus smaller
 * width variants (`name-480.webp`, …) for responsive srcset. SVGs are stored as-is.
 */
class ImageStore
{
    public const MAX_WIDTH = 2400;

    public const WIDTHS = [480, 960, 1600];

    private const QUALITY = 82;

    public static function store(UploadedFile $file, string $dir): string
    {
        $disk = Storage::disk('public_uploads');
        $folder = trim($dir, '/').'/'.now()->format('Y/m');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $name = Str::limit($name, 60, '').'-'.Str::lower(Str::random(6));

        if (strtolower($file->getClientOriginalExtension()) === 'svg') {
            return $file->storeAs($folder, "{$name}.svg", 'public_uploads');
        }

        $manager = new ImageManager(new Driver);
        $image = $manager->read($file->getRealPath())->scaleDown(width: self::MAX_WIDTH);
        $width = $image->width();

        $path = "{$folder}/{$name}.webp";
        $disk->put($path, (string) $image->toWebp(self::QUALITY));

        foreach (self::WIDTHS as $w) {
            if ($w < $width) {
                $variant = $manager->read($file->getRealPath())->scaleDown(width: $w);
                $disk->put("{$folder}/{$name}-{$w}.webp", (string) $variant->toWebp(self::QUALITY));
            }
        }

        return $path;
    }

    public static function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $disk = Storage::disk('public_uploads');
        $base = Str::beforeLast($path, '.');

        $disk->delete([$path, ...array_map(fn ($w) => "{$base}-{$w}.webp", self::WIDTHS)]);
    }

    public static function url(?string $path): ?string
    {
        return $path ? Storage::disk('public_uploads')->url($path) : null;
    }

    /** "url 480w, url 960w, …" for the variants that exist, including the original. */
    public static function srcset(?string $path): ?string
    {
        if (blank($path) || ! str_ends_with($path, '.webp')) {
            return null;
        }

        $disk = Storage::disk('public_uploads');
        $base = Str::beforeLast($path, '.');
        $set = [];

        foreach (self::WIDTHS as $w) {
            if ($disk->exists("{$base}-{$w}.webp")) {
                $set[] = $disk->url("{$base}-{$w}.webp")." {$w}w";
            }
        }

        if (! $set) {
            return null;
        }

        $size = @getimagesize($disk->path($path));
        $set[] = $disk->url($path).' '.($size[0] ?? self::MAX_WIDTH).'w';

        return implode(', ', $set);
    }
}
