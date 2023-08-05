<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\OrderFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderFeedbackController extends Controller
{
    public function createdOrderFeedBack(Request $request)
    {
        try {

            $OrderId = $request['orderid'];
            $MainReason = $request['feedbackreason'];
            $Title = $request['brieftitle'];
            $Feedback = $request['yourfeedback'];
            $UserId = $request['customerid'];

            if ($request->hasFile('refImage')) {
                $file = $request->file('refImage');
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = $request->input('orderid') . '_OID' . '.' . $fileExtension;
                $file->move('uploads/order_feedbacks/', $fileName);
                $feedbackImage = 'uploads/order_feedbacks/' . $fileName;

                OrderFeedback::create([
                    'order_id' => $OrderId,
                    'feedback_reason' => $MainReason,
                    'feedback_title' => $Title,
                    'ref_image' => $feedbackImage,
                    'feedback' => $Feedback,
                    'user_id' => $UserId
                ]);
            } else {
                OrderFeedback::create([
                    'order_id' => $OrderId,
                    'feedback_reason' => $MainReason,
                    'feedback_title' => $Title,
                    'ref_image' => '-',
                    'feedback' => $Feedback,
                    'user_id' => $UserId
                ]);
            }

            return response([
                'status' => 200,
                'message' => 'Feedback created'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
