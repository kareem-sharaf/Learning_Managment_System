<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
       'image',
       'video_id',
       'file_id'
    ];


    public function quizes()
{
    return $this->belongsToMany(Quiz::class);
}
}
