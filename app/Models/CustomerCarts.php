<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CustomerCarts extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_carts';

    protected $fillable = [
        'cart_id',
        'customer_id',
        'cart_title'
    ];

    public $timestamps = false;
}
