<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const TYPES = [
        'quote' => 'Quote request',
        'international' => 'International enquiry',
        'sample' => 'Sample request',
        'callback' => 'Callback request',
        'contact' => 'Contact message',
        'download' => 'Download',
        'job_application' => 'Job application',
    ];

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'quoted' => 'Quoted',
        'won' => 'Won',
        'lost' => 'Lost',
        'spam' => 'Spam',
    ];

    protected $guarded = ['id'];

    protected $casts = ['payload' => 'array', 'utm' => 'array'];

    public function files(): HasMany
    {
        return $this->hasMany(LeadFile::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
