<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'teacher_id',
        'status',
    ];

    public $timestamps = false;
    
    use HasFactory;
}
