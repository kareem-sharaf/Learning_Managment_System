<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    use HasFactory;
    protected $fillable = [
        'class',
        'image_data',
        'form_id'
    ];
    public $timestamps = false;





    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
