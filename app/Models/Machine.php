<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use Sortable;

    public const CATEGORIES = [
        'offset' => 'Offset',
        'digital' => 'Digital',
        'flexo' => 'Flexo',
        'corrugation' => 'Corrugation',
        'finishing' => 'Finishing',
    ];

    protected $guarded = ['id'];

    protected $casts = ['specs' => 'array', 'is_published' => 'boolean'];
}
