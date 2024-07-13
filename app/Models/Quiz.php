<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'duration',
        'total mark',
        'success mark',
        'public',
        'type',
    ];

    public function quizable()
    {
        return $this->morphTo();
    }
     public function teacher()
     {
         return $this->belongsTo(User::class, 'teacher_id');
     }

     public function students()
     {
         return $this->belongsToMany(User::class, 'student_exams', 'quize_id', 'user_id');
     }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function type()
    {
        return $this->morphTo();
    }
}
