<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubMainCategorySub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SubMainCategorySubController extends Controller
{
    /* Fetch all the Sub Main Sub Category Data function starting */
    public function index()
    {
        $subMainSubCatData = DB::table('tbl_submaincategorysub')->get();

        return response()->json([
            'status' => 200,
            'submain_SubcatData' => $subMainSubCatData
        ]);
    }
    /* Fetch all the Sub Main Sub Category Data function ending */

    /* Generare unique Category id function starting */
    public function generateSubMainSubCatId()
    {
        $getSubMainSubCatCount = DB::table('tbl_submaincategorysub')->count();
        $subMainSubCatGenIdd="";
        if ($getSubMainSubCatCount > 0) {
            $getSubMainSubCatId = DB::table('tbl_submaincategorysub')->limit(1)->orderby('id', 'DESC')->get('id');

            $subMainSubCatIdEncode = json_encode($getSubMainSubCatId);
            $subMainSubCarIdDecode = json_decode($subMainSubCatIdEncode, true);

            $currentId = $subMainSubCarIdDecode[0]['id'];
            $subMainSubCatGenIdd = 'AHSMSCAT0' . $currentId + 1;
        }
        else{
            $subMainSubCatGenIdd="AHSMSCAT0";
        }


        return response()->json([
            'status' => 200,
            'subMainSubCatGenId' => $subMainSubCatGenIdd
        ]);
    }
    /* Generare unique Category id function ending */

    /* Create Sub Main Sub Category Fucntion Starting */
    public function createSubMainSubCategory(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

        try {

            $validator = Validator::make($request->all(), [
                'submaincatsub_type' => 'required',
                'submaincat_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }

            $newSubMainSubCat = SubMainCategorySub::create([
                'submaincatsub_type' => $request->input('submaincatsub_type'),
                'submaincat_id' => $request->input('submaincat_id'),
                'created_at'=>$currentTime,
                'updated_at'=>$currentTime,
                'updated_by'=>$request->input('user_email')
            ]);

            return response()->json([
                'status' => 200,
                'subMainSubCatGenId' => $newSubMainSubCat,
                'message' => 'Sub Main Sub Category Created'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Create Sub Main Sub Category Fucntion Ending */




    public function getCategory2ByCategory1($id)
    {
        $subMainSubCatData = DB::table('tbl_submaincategory')->where('maincat_id',$id)->get();

        return response()->json([
            'status' => 200,
            'submain_SubcatData' => $subMainSubCatData
        ]);
    }

    
    public function getCategory3ByCategory2($id)
    {
        $category3 = DB::table('tbl_submaincategorysub')->where('submaincat_id',$id)->get();

        return response()->json([
            'status' => 200,
            'category3' => $category3
        ]);
    }
}
