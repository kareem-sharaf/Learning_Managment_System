<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    use HasFactory;
    protected $fillable=[
        'email',
        'role_id',
        'verificationCode',
        'email_sent_at',
        'verified'
    ];
}
