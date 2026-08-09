<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsPost extends Model
{
    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'show_on_homepage' => 'boolean',
        'seo' => 'array',
    ];
}
