<?php

namespace App\Http\Controllers;

use App\Imports\ExcelImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function uploadExcel(Request $request)
    {
        $file = $request->file('excelFile');

        Excel::import(new ExcelImport, $file);

        $start = now();
        $time = $start->diffInSeconds(now());

        return response()->json([
            'status' => 200,
            'message' => 'Processing done',
            'Time' => $time
        ]);
    }
}
