<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video

extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'video', 'type','exist'];

    public function type()
    {
        return $this->morphTo();
    }

    // public function subject()
    // {
    //     return $this->belongsTo(Subject::class);
    // }

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
        return $this->belongsTo(AD::class, 'ad_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
