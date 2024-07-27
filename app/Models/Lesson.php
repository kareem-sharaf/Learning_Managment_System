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
        'price',
        'video_id',
        'teacher_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function files()
    {
        return $this->hasMany(Files::class);
    }
    public function video()
    {
        return $this->hasOne(Video::class, 'lesson_id');
    }
    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }
    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
