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
        'public',
        'questions_content',
        'subject_id'
    ];


public function questions()
{
    return $this->hasMany(Question::class);
}

public function lessons()
{
    return $this->belongsToMany(Lessons::class);
}

public function subjects()
{
    return $this->belongsToMany(Subjects::class);
}

public function units()
{
    return $this->belongsToMany(Units::class);
}

}
