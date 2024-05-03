<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'image_data',
        'price',
        'video_id',
        'file_id',
        'category_id'
    ];


    // public $timestamps=false;

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }


    public function years_teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject_years', 'subject_id', 'teacher_id')
                    ->withPivot('year_id');
    }
    // public function subjectYears()
    // {
    //     return $this->belongsToMany(SubjectYear::class,'teacher_subject_years');
    // }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function years_users()
    {
        return $this->belongsToMany(User::class, 'teacher_subject_years', 'subject_id', 'user_id')
                    ->withPivot('year_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher')->where('role_id', 3);
    }
}
