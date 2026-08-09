<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sections' => 'array',
        'seo' => 'array',
        'published_at' => 'datetime',
    ];
}
