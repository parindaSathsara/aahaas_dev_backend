<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FCMTokens extends Model
{
    use HasFactory;


    protected $table = 'user_fcm_tokens';

    protected $fillable = [
        'id',
        'user_id',
        'token',
    ];
}
