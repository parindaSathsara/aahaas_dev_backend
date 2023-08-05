<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\imports\HotelBulk;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class HotelBulkController extends Controller
{
    public function hotelBulkUpload(Request $request)
    {
        try {

            $file = $request->file('excelupload');

            Excel::import(new HotelBulk, $file);

            $start = now();
            $time = $start->diffInSeconds(now());


            return response()->json([
                'status' => 200,
                'message' => 'Processing done',
                'Time' => $time
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
