<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelTermsConditions extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_terms_conditions';

    protected $fillable = [
        'hotel_id',
        'general_tc',
        'cancellation_policy',
        'cancellation_deadline',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
