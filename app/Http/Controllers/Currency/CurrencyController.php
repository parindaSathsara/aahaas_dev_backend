<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    public function getAllCurrecncies()
    {
        $AllCurrency = DB::table('currencies')->select('*')->get();

        return response(['status' => 200, 'curr_data' => $AllCurrency]);
    }

    public function getCurrencyById($id)
    {
        $CurrencyById = DB::table('currencies')->where('id', $id)->first();

        return response([
            'status' => 200,
            'curr_code' => $CurrencyById->code,
            'ex_rate' => $CurrencyById->exchange_rate
        ]);
    }
}
