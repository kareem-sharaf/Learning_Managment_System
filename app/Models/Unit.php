<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'image_url',
        'video_id',
        'file_id',
        'subject_id',
    ];
    public $timestamps=false;

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizable');
    }
}
