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
        'public'
    ];


public function questions()
{
    return $this->belongsToMany(Question::class);
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
