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
    {
        return $this->hasOne(Video::class, 'ad_id');
    }
}
