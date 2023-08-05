<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelVendor extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_vendor';

    protected $fillable = [
        'hotel_id',
        'hotel_email',
        'official_address',
        'hotel_contact',
        'key_person',
        'key_contact_number',
        'key_contact_email',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
