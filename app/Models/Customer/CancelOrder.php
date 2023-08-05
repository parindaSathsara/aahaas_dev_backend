<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CancelOrder extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_cancel_orders';

    protected $fillable = [
        'order_id',
        'prod_id',
        'prod_title',
        'reason',
        'other_remarks',
        'ref_image',
        'user_id',
        'status'
    ];

    public $timestamps = false;
}
