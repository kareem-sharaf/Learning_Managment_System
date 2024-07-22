<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'content', 'subject_id', 'unit_id', 'lesson_id'];

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

    public function leason()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
