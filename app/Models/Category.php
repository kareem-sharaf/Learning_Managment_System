<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'category',
        'image_url',
    ];

    public $timestamps = false;

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function teachers()
    {
        return $this->hasMany(User::class, 'role_id', 3);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'favorite_categories', 'category_id', 'user_id');
    }
}
