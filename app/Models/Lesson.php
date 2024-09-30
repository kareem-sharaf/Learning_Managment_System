<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'unit_id',
        'image',
        // 'price',
        'video_id',
        'file_id',
        'teacher_id',
        'exist'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }
    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
    public function youtubeVideos()
    {
        return $this->hasMany(YouTube1::class);
    }
}
