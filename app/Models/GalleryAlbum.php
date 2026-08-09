<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryAlbum extends Model
{
    protected $guarded = [];

    protected $casts = [
        'album_date' => 'date',
        'is_published' => 'boolean',
    ];
}
