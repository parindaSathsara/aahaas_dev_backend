<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_customer';

    protected $fillable = [
        'customer_id',
        'customer_fname',
        'contact_number',
        'customer_email',
        'customer_nationality',
        'customer_profilepic',
        'customer_status',
        'customer_address',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}
