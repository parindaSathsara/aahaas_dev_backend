<?php

namespace App\Http\Controllers\Flights;

use App\Http\Controllers\Controller;
use App\Models\Flights\AirportCodes;
use Illuminate\Http\Request;

class AirportCodesController extends Controller
{
    public function getAirportCodes(Request $request)
    {
        try {

            $path = "Data/AirportCodes.json";
            $json = json_decode(file_get_contents($path), true);

            return $json;
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }
}
