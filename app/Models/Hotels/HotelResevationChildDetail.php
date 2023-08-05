<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelResevationChildDetail extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotelresevation_child_details';

    protected $fillable = [
        'resevation_no',
        'child_name',
        'child_age'
    ];

    public $timestamps = false;
}
