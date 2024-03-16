<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;
    protected $fillable=[
        'year',
        'stage_id'
    ];
    public $timestamps=false;





    public function subjects()
    {
        return $this->belongsToMany(Subject::class,'subject_year');
    }



    public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'teacher_year');
    }
}


