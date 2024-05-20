<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    public function User()
    {
        return $this->hasMany(User::class);
    }
    use HasFactory;
    protected $fillable = [
        'user_id',
        'message',
    ];
}
