<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupNotice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'target_pages' => 'array',
        'show_close_button' => 'boolean',
        'allow_do_not_show_again' => 'boolean',
        'is_active' => 'boolean',
    ];
}
