<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use Sortable;

    public const CATEGORIES = [
        'company' => 'Company profile',
        'catalogue' => 'Catalogue',
        'datasheet' => 'Data sheet',
    ];

    protected $guarded = ['id'];

    protected $casts = ['requires_email' => 'boolean', 'is_published' => 'boolean'];
}
