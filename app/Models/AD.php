<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AD extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'description',
        'image_data',
        'year_id',
        'stage_id',
        'isExpired'
    ];
}
