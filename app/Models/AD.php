<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AD extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'category_id',
        'video_id',
        'isExpired'
    ];
    public function videos()
<<<<<<< HEAD
{
    return $this->hasMany(Video::class);
}
public function youtubeVideos()
    {
        return $this->hasMany(YouTube1::class);
=======
    {
        return $this->hasOne(Video::class, 'ad_id');
>>>>>>> d3a832360c4e6969fe6ef18cb3fc577a21b64d9d
    }
}
