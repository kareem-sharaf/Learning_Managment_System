<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'vedio',
        'stage_id',
        'year_id'
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }
}
