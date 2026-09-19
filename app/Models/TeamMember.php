<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use Sortable;

    public const DEPARTMENTS = [
        'leadership' => 'Leadership',
        'design' => 'Design',
        'sales' => 'Sales & coordination',
        'quality' => 'Quality',
        'production' => 'Production',
    ];

    protected $guarded = ['id'];

    protected $casts = ['is_leadership' => 'boolean', 'is_published' => 'boolean'];
}
