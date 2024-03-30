<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    use HasFactory;
    protected $fillable = [
        'class'
    ];
    public $timestamps = false;

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }
}
