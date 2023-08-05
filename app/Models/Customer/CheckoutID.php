<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CheckoutID extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_checkout_ids';

    protected $fillable = [
        'user_id',
        'checkout_status',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'payment_type',
        'pay_category'
    ];

    public $timestamps = false;
}
