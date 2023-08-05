<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class OnlineTransfer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_online_transfers';

    protected $fillable = [
        'reference_no',
        'reference_email',
        'reference_Image',
        'user_id',
        'checkout_id'
    ];

    public $timestamps = false;
}
