<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class ResevationServiceType extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_servicedetail';

    protected $fillable = [
        'resevation_no',
        'service_type',
        'date',
        'adult_count',
        'child_count',
        'unit_price'
    ];

    public $timestamps = false;
}
