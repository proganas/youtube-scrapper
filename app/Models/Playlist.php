<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'playlist_id',
        'title',
        'description',
        'thumbnail',
        'channel_name',
        'category',
    ];
}
