<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\CancelOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelOrderController extends Controller
{
    public function index()
    {
        return 'Test';
    }

    public function cancelOrReturnOrder(Request $request)
    {
        try {

            $Reason = $request['retcanreason'];
            $UserEmail = $request['useremail'];
            $OrderRemark = $request['orderremarks'];
            $OrderId = $request['orderid'];
            $MainId = $request['mainid'];
            $Category = $request['category'];
            $Type = $request['type'];
            $UserId = $request['userid'];
            $Platform = $request['platform'];

            // if ($Type == 'Education') {
            // }

            if ($request->hasFile('formImage')) {
                $file = $request->file('formImage');
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = $request->input('orderid') . '_OID' . '.' . $fileExtension;
                $file->move('uploads/cancelreturn_images/', $fileName);
                $RefImage = 'uploads/cancelreturn_images/' . $fileName;

                CancelOrder::create([
                    'order_id' => $OrderId,
                    'prod_id' => $MainId,
                    'prod_title' => $Category,
                    'reason' => $Reason,
                    'other_remarks' => $OrderRemark,
                    'ref_image' => $RefImage,
                    'user_id' => $UserId,
                    'status' => $Type
                ]);

                DB::select(DB::raw("UPDATE tbl_checkouts SET status='$Type',delivery_status='$Type' WHERE id='$MainId'"));
            } else {

                CancelOrder::create([
                    'order_id' => $OrderId,
                    'prod_id' => $MainId,
                    'prod_title' => $Category,
                    'reason' => $Reason,
                    'other_remarks' => $OrderRemark,
                    'ref_image' => '-',
                    'user_id' => $UserId,
                    'status' => $Type
                ]);

                DB::select(DB::raw("UPDATE tbl_checkouts SET status='$Type',delivery_status='$Type' WHERE id='$MainId'"));
            }

            return response(['status' => 200, 'message' => 'Data saved success']);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
