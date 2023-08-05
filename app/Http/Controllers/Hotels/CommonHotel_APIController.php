<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\CommonHotel_API;

class CommonHotel_APIController extends Controller
{
    public $aahaas_hotel;

    public function __construct()
    {
        $this->aahaas_hotel = new CommonHotel_API();
    }

    //push aahaas hotel to meta table
    public function pushAahaasHotel()
    {
        $response = $this->aahaas_hotel->aahaasHotelPush();

        return $response;
    }
}
