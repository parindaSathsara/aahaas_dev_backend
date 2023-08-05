<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ExcelController extends Controller
{
    public function uploadExcelDataSheet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload_excel' => 'required|file|mimes:xls,xlsx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'validation_error' => $validator->messages()
            ]);
        }
        if (!Auth::check()) {
            return response()->json([
                'status' => 403,
                'login_error' => 'Session Expired, Please log again'
            ]);
        }

        // $exceLReader = new Spread


        return 'Success';
    }
}
