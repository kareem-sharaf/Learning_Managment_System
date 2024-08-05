<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YouTube1 extends Model
{
    use HasFactory;
    protected $fillable = [

        'title',
        'description',
        'tags',
        'video_id',
        'video_url',
        'subject_id',
        'unit_id',
        'lesson_id',
        'ads_id'
    ];



    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function ads()
    {
        return $this->belongsTo(Ad::class);
    }
}
