<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'quiz_id',
    'mark',
    'status',
    'date'
];

public function quiz()
{
    return $this->belongsTo(Quiz::class, 'quize_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
