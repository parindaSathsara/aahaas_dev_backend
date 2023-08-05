<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubMainCategory;
use App\Models\SubMainCategorySub;
use App\Models\SubMiniCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class FilterController extends Controller
{
    public function getMainSubCategories()
    {
        $mainSubCategories = DB::table('tbl_submaincategory')->get();

        return response()->json([
            'status' => 200,
            'main_subcat_data' => $mainSubCategories
        ]);
    }

    public function getSubMainSubCategories()
    {
        $subMainSubCategories = DB::table('tbl_submaincategorysub')->get();

        return response()->json([
            'status' => 200,
            'main_subcatsub_data' => $subMainSubCategories
        ]);
    }

    public function getManufactures()
    {
        $manufactures = DB::table('tbl_manufacture')->get();

        return response()->json([
            'status' => 200,
            'manufactures_data' => $manufactures
        ]);
    }


    public function getCategories()
    {
        $categories = DB::table('tbl_maincategory')
            ->get();

        $subCategory = DB::table('tbl_submaincategory')
            ->get();

        $subSubCategory = DB::table('tbl_submaincategorysub')
            ->get();
            

        $subSubSubCategory = DB::table('tbl_submaincategorysubsub')
            ->get();

        return response()->json([
            'status' => 200,
            'categories' => $categories,
            'subCategories' => $subCategory,
            'subSubCategory' => $subSubCategory,
            'subSubSubCategory' => $subSubSubCategory,
            'message' => "Success"
        ]);
    }
}
