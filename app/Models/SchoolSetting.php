<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'social_links' => 'array',
    ];
}
