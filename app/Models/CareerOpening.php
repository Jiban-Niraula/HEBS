<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerOpening extends Model
{
    protected $guarded = [];

    protected $casts = [
        'requirements' => 'array',
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];
}
