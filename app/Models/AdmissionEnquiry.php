<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionEnquiry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'privacy_consent' => 'boolean',
        'internal_notes' => 'array',
    ];
}
