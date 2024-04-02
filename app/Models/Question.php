<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable=[
        'text',
        'answer',
        'mark',
        'subject_year_id',
    ];


    public function quizes()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
