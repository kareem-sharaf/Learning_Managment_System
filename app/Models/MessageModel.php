<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    use HasFactory;



    protected $fillable = ['user_id', 'message'];






    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
