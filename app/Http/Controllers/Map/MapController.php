<?php

namespace App\Http\Controllers\Map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapController extends Controller
{
    //

    public function getmap($lat, $long)
    {

        $data = ['lat' => $lat, 'long' => $long];

        return view('Map.map', $data);
    }


    public function getmapTest()
    {
        return view('SellerRegistration.SellerRegistration');
    }
}
