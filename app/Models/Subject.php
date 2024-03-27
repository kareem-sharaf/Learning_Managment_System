<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image_data',
        'price',
        'video_id',
        'file_id',
        'class_id'
    ];

     
    // public $timestamps=false;

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function years()
    {
        return $this->belongsToMany(Year::class,'subject_years');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function class()
    {
        return $this->belongsTo(classification::class);
    }

}
