<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;
use App\Models\DiscountType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{

    /* Create discount types function starting */
    public function createNewBrand(Request $request)
    {
        try{

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();



            $validator=Validator::make($request->all(),[
                'brandName'=>'required',
                'brandDescription'=>'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status'=>200,
                    'validation_error'=>$validator->messages()
                ]);
            }
            else{
                $newBrand=Brands::create([
                    'brandName'=>$request->input('brandName'),
                    'brandDescription'=>$request->input('brandDescription'),
                    'category1'=>$request->input('category1'),
                    'category2'=>$request->input('category2'),
                    'category3'=>$request->input('category3'),
                    'created_at'=>$currentTime,
                    'updated_at'=>$currentTime
                ]);
            }


            return response()->json([
                'status'=>200,
                'message'=>'Successfull',
                'discountData'=>$newBrand
            ]);

        }catch(\Exception $exception){

            return response()->json([
                'status'=>400,
                'message'=> throw $exception
            ]);

        }
    }

    public function getAllBrands(){
        $brands=DB::table('tbl_brands')->get();

        return response()->json([
            'status'=>200,
            'brands'=>$brands,
        ]);
    }


    /* Create discount types function Ending */
}
