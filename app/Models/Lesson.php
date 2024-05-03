<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'teacher_id',
       'video_id',
       'image',
       'file_id'
       ,'price'];



       public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
    public function file()
    {
        return $this->belongsTo(Files::class);
    }

}
