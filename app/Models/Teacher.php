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
        'description',
        'class_id'
    ];


// public function subjectYears()
// {
//     return $this->belongsToMany(SubjectYear::class,'teacher_subject_years');
// }

public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject_years', 'teacher_id', 'subject_id')
                    ->withPivot('year_id');
    }
public function years()
{
    return $this->belongsToMany(Year::class);
}
public function subjects_teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject_years', 'subject_id', 'teacher_id')
                    ->withPivot('year_id');
    }

public function category()
{
    return $this->belongsTo(Category::class);
}

public function favorites()
{
    return $this->morphMany(Favorite::class, 'favoritable');
}

}
