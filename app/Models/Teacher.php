<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image_data',
    ];


public function subjects()
{
    return $this->belongsToMany(Subject::class, 'teacher_subject');
}



public function years()
{
    return $this->belongsToMany(Year::class, 'teacher_year');
}
}
