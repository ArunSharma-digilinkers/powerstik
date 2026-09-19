<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LeadFile extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::deleted(fn (self $file) => Storage::disk('local')->delete($file->path));
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
