<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $casts = ['is_published' => 'boolean'];
}
