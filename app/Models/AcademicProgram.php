<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicProgram extends Model
{
    protected $guarded = [];

    protected $casts = [
        'learning_objectives' => 'array',
        'curriculum_overview' => 'array',
        'activities' => 'array',
        'is_published' => 'boolean',
    ];
}
