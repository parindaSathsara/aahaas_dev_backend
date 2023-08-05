<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubMainCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SubMainCategoryController extends Controller
{
    /* Fetch all the Sub Main Category Data function starting */
    public function index()
    {
        $subMainCatData = DB::table('tbl_submaincategory')->get();

        return response()->json([
            'status' => 200,
            'submainsub_catData' => $subMainCatData
        ]);
    }
    /* Fetch all the Sub Main Category Data function ending */

    /* Generare unique Category id function starting */
    public function generateSubMainCatId()
    {   
        $subMainCatGenId="";
        $getSubMainCatCount = DB::table('tbl_submaincategory')->count();
        if ($getSubMainCatCount > 0) {
            $getSubMainCatId = DB::table('tbl_submaincategory')->limit(1)->orderby('id', 'DESC')->get('id');

            $subMainCatIdEncode = json_encode($getSubMainCatId);
            $subMainCarIdDecode = json_decode($subMainCatIdEncode, true);

            $currentId = $subMainCarIdDecode[0]['id'];
            $subMainCatGenId = 'AHMSUBCAT0' . $currentId + 1;
        }
        else{
            $subMainCatGenId = 'AHMSUBCAT01';
        }


        return response()->json([
            'status' => 200,
            'subMainCatGenId' => $subMainCatGenId
        ]);
    }
    /* Generare unique Category id function ending */

    /* Create Sub Main Category Fucntion Starting */
    public function createSubMainCategory(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

        try {

            $validator = Validator::make($request->all(), [
                'submaincat_type' => 'required',
                'maincat_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }

            $newSubMainCat = SubMainCategory::create([
                'submaincat_type' => $request->input('submaincat_type'),
                'maincat_id' => $request->input('maincat_id'),
                'created_at'=>$currentTime,
                'updated_at'=>$currentTime,
                'updated_by'=>'test@testemail.com'
            ]);

            return response()->json([
                'status' => 200,
                'newSubMainCat' => $newSubMainCat,
                'messege' => 'Sub Main Category Created'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Create Sub Main Category Fucntion Ending */
}
