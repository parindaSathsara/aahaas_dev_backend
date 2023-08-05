<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payments\NewPaymentIPG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewPaymentIPGController extends Controller
{
    public $NewPayment;

    public function __construct()
    {
        $this->NewPayment = new NewPaymentIPG();
    }

    public function createNewPaymentLink(Request $request)
    {
        try {

            $OrderId = $request['order_id'];
            $OrderAmount = $request['amount'];
            $CustomerEmail = $request['customer_email'];
            $CustomerCurrency = $request['cus_currency'];

            // return $request;

            $response = $this->NewPayment->createNewPayment($OrderId, $OrderAmount, $CustomerEmail, $CustomerCurrency);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function createNewPaymentLinkMobile(Request $request)
    {
        try {

            $OrderId = $request['order_id'];
            $OrderAmount = $request['amount'];
            $CustomerEmail = $request['customer_email'];

            $response = $this->NewPayment->createNewPaymentMobile($OrderId, $OrderAmount, $CustomerEmail);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //payment checkout screen
    public function getPaymentCheckout($sessionid, $versionid, $oid)
    {
        try {

            $SqlQuery = DB::table('tbl_checkout_ids')->where('tbl_checkout_ids.id', $oid)
                ->join('payments_ipg', 'tbl_checkout_ids.id', '=', 'payments_ipg.order_id')
                // ->join('users', 'tbl_checkout_ids.user_id', '=', 'users.id')
                ->first();

            $data = ['dataset' => $SqlQuery, 'session_id' => $sessionid, 'version' => $versionid, 'oid' => $oid];

            // return $SqlQuery;

            return view('Payment.PaymentCheckout', $data);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //payment response
    public function getPaymentRes($id)
    {
        try {

            $response = $this->NewPayment->getPaymentResponse($id);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
