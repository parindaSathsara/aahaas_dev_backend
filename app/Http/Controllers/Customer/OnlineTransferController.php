<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\OnlineTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineTransferController extends Controller
{
    public function createNewOnlineTransfer(Request $request)
    {
        try {

            $ReferenceNo = $request['referenceno'];
            $ReferenceEmail = $request['refemail'];
            $UserId = $request['uid'];
            $orderId = $request['oid'];

            $onlineTrans_Images = array();

            if ($request->hasFile('image0')) {
                $filesLength = $request->input('imageLength');
                $intLength = (int)$filesLength - 1;

                for ($x = 0; $x <= $intLength; $x++) {
                    $file = $request->file('image' . $x);
                    $fileExtension = $file->getClientOriginalExtension();
                    $fileName = $ReferenceNo . $file->getClientOriginalName();
                    $upload_path = 'uploads/onlinetransferimages/';
                    $image_url = $upload_path . $fileName;
                    $file->move($upload_path, $fileName);
                    $onlineTrans_Images[] = $image_url;
                }

                OnlineTransfer::create([
                    'reference_no' => $ReferenceNo,
                    'reference_email' => $ReferenceEmail,
                    'reference_Image' => implode(',', $onlineTrans_Images),
                    'user_id' => $UserId,
                    'checkout_id' => $orderId
                ]);
            }

            return response([
                'status' => 200,
                'message' => 'Transfer created successfully'
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
