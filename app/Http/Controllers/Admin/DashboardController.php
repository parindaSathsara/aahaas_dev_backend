<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function fetchAllOrderCount()
    {
        $orderCount = DB::table('tbl_checkouts')->get()->count();

        return response([
            'status' => 200,
            'data_response' => $orderCount
        ]);
    }

    public function fetchActiveHotelCount()
    {
        $hotelCount = DB::table('tbl_hotel')->get()->count();

        return response([
            'status' => 200,
            'data_response' => $hotelCount
        ]);
    }

    public function fetchAllActiveCustomers()
    {
        $cxCount = DB::table('tbl_customer')->get()->count();

        return response([
            'status' => 200,
            'data_response' => $cxCount
        ]);
    }
}
