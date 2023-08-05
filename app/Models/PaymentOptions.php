<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PaymentOptions extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_brands';

    protected $fillable = [
        'payment_id',
        'payment_option_name',
        'payment_option_description',
    ];

    public $timestamps = false;
}
