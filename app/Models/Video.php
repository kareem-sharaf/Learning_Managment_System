<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function leason()
    {
        return $this->belongsTo(Lesson::class);
    }
    protected $fillable =[
        'name',
        'video',
        'leeson_id',
        'unit_id',
        
    ];
}
