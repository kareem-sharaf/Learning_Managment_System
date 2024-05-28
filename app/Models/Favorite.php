<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'favoritable_id',
        'favoritable_type',
        'favoritable_name'
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_id', 4);
    }

    public function favoritable()
    {
        return $this->morphTo();
    }
}
