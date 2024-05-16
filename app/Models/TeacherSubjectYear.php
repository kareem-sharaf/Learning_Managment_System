<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubjectYear extends Model
{
    protected $fillable=[
        'user_id',
        'subject_id',
        'year_id'
    ];
    use HasFactory;
}
