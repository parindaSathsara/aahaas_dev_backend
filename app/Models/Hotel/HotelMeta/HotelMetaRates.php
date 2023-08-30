<?php

namespace App\Models\Hotel\HotelMeta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;

class HotelMetaRates extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'aahaas_hotel_meta';

    public $timestamps = false;

    protected $fillable = [
        'hotelCode',
        'hotelName',
        'roomCode',
        'roomName',
        'roomCategory',
        'rateKey',
        'rateClass',
        'rateType',
        'net',
        'adultRate',
        'childWithBedRate',
        'childWithNoBedRate',
        'allotment',
        'paymentType',
        'packaging',
        'boardCode',
        'boardName',
        'cancellationAmount',
        'cancellationFrom',
        'taxIncluded',
        'taxAmount',
        'taxCurrency',
        'clientAmount',
        'clientCurrency',
        'allIncluded',
        'offerCode',
        'offerName',
        'offerAmount',
        'discountLimit',
        'minRate',
        'maxRate',
        'currency',
        'checkIn',
        'checkOut',
        'rooms',
        'adults',
        'children',
        'childrenAges',
        'sortCriteria',
        'source',
        'userId',
    ];

    //Feeding rates according to hotels
    public function createHotelRates()
    {
    }
}
