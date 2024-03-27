<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserValidation extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'father_name',
        'role_id',
        'validation_code'
    ];
}
