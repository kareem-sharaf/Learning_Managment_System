<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        'video_id',
        'title',
        'description',
        'thumbnail_url',
        'video_url',
        'views',
        'likes',
        'dislikes',
        'category_id',
        'privacy_status',
    ];
}
