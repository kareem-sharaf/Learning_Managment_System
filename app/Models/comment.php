<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',

        'video_id',
        'content',
        'reply_to'
    ];
public function user()
{
    return $this->belongsTo(User::class);
}

public function parentComment()
    {
        return $this->belongsTo(Comment::class, 'reply_to');
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_to');
    }
public function video()
    {
        return $this->belongsTo(Video::class);
    }

}
