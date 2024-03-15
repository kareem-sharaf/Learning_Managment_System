<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'subject_id',
        'year_id'
    ];


public function subjects()
{
    return $this->belongsToMany(Subject::class, 'teacher_subject');
}

}
