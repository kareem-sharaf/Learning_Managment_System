<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubjectYear extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'subject_id',
        'year_id'
    ];
    public $timestamps = true;
}
