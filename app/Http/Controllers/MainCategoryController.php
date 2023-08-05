<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class MainCategoryController extends Controller
{
    /* Fetch all the Main Category Data function starting */
    public function index()
    {
        $mainCatData = DB::table('tbl_maincategory')->get();

        return response()->json([
            'status' => 200,
            'main_catData' => $mainCatData
        ]);
    }
    /* Fetch all the Main Category Data function ending */

    /* Generare unique Category id function starting */
    public function generateMainCatId()
    {
        $getMainCatCount = DB::table('tbl_maincategory')->count();
        $mainCatGenId="";

        if ($getMainCatCount > 0) {
            $getMainCatId = DB::table('tbl_maincategory')->limit(1)->orderby('id', 'DESC')->get('id');

            $mainCatIdEncode = json_encode($getMainCatId);
            $mainCarIdDecode = json_decode($mainCatIdEncode, true);

            $currentId = $mainCarIdDecode[0]['id'];
            $mainCatGenId = 'AHMCAT0' . $currentId + 1;
        }
        else{
            $mainCatGenId = 'AHMCAT01';
        }


        return response()->json([
            'status' => 200,
            'mainCatGenId' => $mainCatGenId
        ]);
    }
    /* Generare unique Category id function ending */

    /* Create Main Category Fucntion Starting */
    public function createMainCategory(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

        try {

            $validator = Validator::make($request->all(), [
                'maincat_type' => 'required|unique:tbl_maincategory'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }

            $newMainCat = MainCategory::create([
                'maincat_type' => $request->input('maincat_type'),
                'created_at'=>$currentTime,
                'updated_at'=>$currentTime,
                'updated_by'=>$request->input('user_email')
            ]);

            return response()->json([
                'status' => 200,
                'newMainCat' => $newMainCat,
                'message' => 'Main Category Created'
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Create Main Category Fucntion Ending */
}
