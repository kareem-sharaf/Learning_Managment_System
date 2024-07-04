<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
<<<<<<< Updated upstream
    protected $fillable = ['name', 'video', 'subject_id', 'unit_id', 'lesson_id'];

public function subject()
{
    return $this->belongsTo(Subject::class);
=======
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function leason()
    {
        return $this->belongsTo(Lesson::class);
    }
    protected $fillable =[
        'name',
        'video',
        'leeson_id',
        'subject_id',
        'unit_id',
        'ad_id',
        'category_id',

    ];
>>>>>>> Stashed changes
}

public function unit()
{
    return $this->belongsTo(Unit::class);
}

public function lesson()
{
    return $this->belongsTo(Lesson::class);
}
}
