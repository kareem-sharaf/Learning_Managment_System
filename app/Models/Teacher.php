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
        'description'
    ];


public function subjectYears()
{
    return $this->belongsToMany(SubjectYear::class,'teacher_subject_years');
}
}
