<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookmarkable_id',
        'bookmarkable_type',
        'bookmark_name'
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_id', 4);
    }

    public function bookmarkable()
    {
        return $this->morphTo();
    }
}

