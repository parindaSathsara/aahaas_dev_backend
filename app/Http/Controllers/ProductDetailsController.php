<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;
use App\Models\DiscountType;
use App\Models\ProductDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductDetailsController extends Controller
{

    /* Create discount types function starting */
    public function createListingDetails(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(), [
                'listing_id' => 'required',
                // 'category1' => 'required',
                // 'category2' => 'required',
                // 'category3' => 'required',
                'delivery_type' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'status' => 200,
                    'validation_error' => $validator->messages()
                ]);
            } else {


                $newProductDetails = ProductDetails::create([
                    'listing_id'=>$request->input('listing_id'),
                    'category1' => $request->input('category1'),
                    'category2' => $request->input('category2'),
                    'category3' => $request->input('category3'),
                    'search_tags' => $request->input('search_tags'),
                    'group_tags' => $request->input('group_tags'),
                    'priority' => $request->input('priority'),
                    'delivery_type' => $request->input('delivery_type'),
                    'payment_options'=>$request->input('payment_options')
                ]);


                return response()->json([
                    'status' => 200,
                    'message' => 'Successfull',
                    'productDetails' => $newProductDetails
                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }



    /* Create discount types function Ending */
}
