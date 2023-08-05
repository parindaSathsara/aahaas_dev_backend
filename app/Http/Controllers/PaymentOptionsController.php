<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class PaymentOptionsController extends Controller
{

    /* Create discount types function starting */

    public function getAllOptions(){
        $payment_options=DB::table('tbl_payment_options')->get();

        return response()->json([
            'status'=>200,
            'payment_options'=>$payment_options,
        ]);
    }


    /* Create discount types function Ending */
}
