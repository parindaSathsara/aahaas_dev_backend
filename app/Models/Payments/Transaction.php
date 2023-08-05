<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'ahs_transactions';

    protected $fillable = [
        'pay_id',
        'auth_token',
        'trans_token',
        'auth_status',
        'result',
        'payment_status',
        'user_ip',
    ];

    public $timestamps = false;
}
