<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function leason()
    {
        return $this->belongsTo(Leason::class);
    }
    protected $fillable =[
        'name',
        'file',
        'leeson_id',
        'unit_id',
        
    ];
}
