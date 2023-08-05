<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubMiniCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SubMiniCategoryController extends Controller
{
    /* Fetch all the Sub Mini Category Data function starting */
    public function index()
    {
        $subMiniCatData = DB::table('tbl_manufacture')->get();

        return response()->json([
            'status'=>200,
            'subMini_CatData'=>$subMiniCatData
        ]);
    }
    /* Fetch all the Sub Mini Category Data function ending */

    /* Generare unique Category id function starting */
    public function generateSubMiniCatId()
    {
        $getSubMiniCatId = DB::table('tbl_manufacture')->limit(1)->orderby('id','DESC')->get('id');

        $subMiniCatIdEncode = json_encode($getSubMiniCatId);
        $subMiniCarIdDecode = json_decode($subMiniCatIdEncode,true);

        $currentId = $subMiniCarIdDecode[0]['id'];
        $subMiniCatGenId = 'AHSUBMINCAT0'.$currentId+1;

        return response()->json([
            'status'=>200,
            'subMiniCatGenId'=>$subMiniCatGenId
        ]);
    }
    /* Generare unique Category id function ending */

    /* Create Sub Mini Category Fucntion Starting */
    // public function createSubMiniCategory(Request $request)
    // {
    //     try{

    //         $validator = Validator::make($request->all(),[
    //             'manufacture_id'=>'required|unique:tbl_subminicategory',
    //             'manufacture_title'=>'required|unique:tbl_subminicategory',
    //             'submaincatsub_id'=>'required'
    //         ]);

    //         if($validator->fails())
    //         {
    //             return response()->json([
    //                 'status'=>400,
    //                 'validation_error'=>$validator->messages()
    //             ]);
    //         }
            
    //         $newSubMiniCat = SubMiniCategory::create([
    //             'subminicat_id'=>$request->input('subminicat_id'),
    //             'subminicat_type'=>$request->input('subminicat_type'),
    //             'submaincatsub_id'=>$request->input('submaincatsub_id')
    //         ]);

    //         return response()->json([
    //             'status'=>200,
    //             'newSubMiniCat'=>$newSubMiniCat,
    //             'status'=>'Sub Mini Category Created'
    //         ]);

    //     }catch(\Exception $exception){

    //         return response()->json([
    //             'status'=>400,
    //             'message'=>$exception->getMessage()
    //         ]);

    //     }
    // }
    /* Create Sub Mini Category Fucntion Ending */
}
