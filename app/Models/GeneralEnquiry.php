<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralEnquiry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'privacy_consent' => 'boolean',
    ];
}
