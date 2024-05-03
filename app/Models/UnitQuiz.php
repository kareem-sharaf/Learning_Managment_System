<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitQuiz extends Model
{
    use HasFactory;
    protected $fillable=[
        'unit_id',
        'quiz_id'
    ];

    public function quizes()
    {
        return $this->hasMany(Quiz::class);
    }
}
