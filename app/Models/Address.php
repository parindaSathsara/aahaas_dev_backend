<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Address extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_addresses';

    protected $fillable = [
        'customer_id',
        'street',
        'other',
        'city',
        'province',
        'latitude',
        'longtitude',
        'zip_code',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}
