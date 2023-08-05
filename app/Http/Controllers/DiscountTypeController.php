<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscountType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiscountTypeController extends Controller
{
    /* Get all the discount types function starting */
    public function index()
    {
        $discount_types = DB::table('tbl_discount_types')->get();

        return response()->json([
            'status'=>200,
            'discount_type_data'=>$discount_types
        ]);
    }
    /* Get all the discount types function Ending */

    /* Create discount types function starting */
    public function createNewDiscountType(Request $request)
    {
        try{

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(),[
                'discount_title_type'=>'required'
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'validation_error'=>$validator->messages()
                ]);
            }

            $newDiscountType = DiscountType::create([
                'discount_title_type'=>$request->input('discount_title_type'),
                'created_at'=>$currentTime,
                'updated_at'=>$currentTime,
                'updated_by'=>'viraj@gmail.com'
            ]);

            return response()->json([
                'status'=>200,
                'message'=>'Successfull',
                'discountData'=>$newDiscountType
            ]);

        }catch(\Exception $exception){

            return response()->json([
                'status'=>400,
                'message'=> throw $exception
            ]);

        }
    }
    /* Create discount types function Ending */
}
