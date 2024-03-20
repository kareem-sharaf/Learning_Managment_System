<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectYear extends Model
{
    use HasFactory;


    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject_years');
    }
}
