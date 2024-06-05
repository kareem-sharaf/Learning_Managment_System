<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

  

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function files()
    {
        return $this->hasMany(Files::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    public function quizes()
{
    return $this->belongsToMany(Quiz::class);
}
    protected $fillable = [
        'name',
        'description',
        'unit_id',
       'video_id',
       'image',
       'file_id'
       ,'price'];
}
