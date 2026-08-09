<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_pinned' => 'boolean',
        'show_on_homepage' => 'boolean',
        'show_in_announcement' => 'boolean',
        'show_as_popup' => 'boolean',
    ];
}
