<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

  


       public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function video()
    {
        return $this->hasMany(Video::class);
    }
    public function file()
    {
        return $this->hasMany(Files::class);
    }
    public function quizes()
{
    return $this->belongsToMany(Quiz::class);
}
    protected $fillable = [
        'name',
        'description',
        'unit_id',
       'video',
       'image',
       'file'
       ,'price'];
}
