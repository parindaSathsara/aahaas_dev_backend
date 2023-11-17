<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Customer\CheckoutID;
use App\Models\Payments\PaymentAPICall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{
    public $makeApiPaymentCall;
    public $token;

    public function __construct()
    {
        $this->makeApiPaymentCall = new PaymentAPICall();
        $this->token = session()->get('payment_access_token');
    }

    public function requestPaymentUrl(Request $request)
    {
        $randomNo = Str::random(4);
        $timestamp = Carbon::now()->timestamp;
        $qtyNo = $randomNo . $timestamp;

        $paymentMethod = $request->paymentType;

        $amount = $request->amount;
        $amountPayNow = $request->amountPayNow;
        $email = $request->email;
        $quotationNo = $qtyNo;
        $bankId = 1;
        $chargesId = 0;

        if ($paymentMethod == 'MinimumPayment') {
            $amount = $amountPayNow;
            $paymentType = 'partialy_payment_by_credit_card';
        } else if ($paymentMethod == 'CustomPayment') {
            $amount = $request->custom_payment;
            $paymentType = 'partialy_payment_by_credit_card';
        } else if ($paymentMethod == 'FullPayment') {
            $amount = $amount;
            $paymentType = 'full_payment_by_credit_card';
        }

        Session::put('customerpayments.general_data.payment_type', $paymentType);
        Session::put('customerpayments.general_data.payment_amount', $amount);
        Session::put('customerpayments.general_data.pay_now', $amount);
        Session::put('customerpayments.general_data.pay_on_delivery', $request->amount - $amount);

        $method = 'POST';
        $url = '/api/payments/createLink';
        // $token = '129|Ty0JbDQzmjw5SCqT0xTnTVcCg8J0asTrUQTGT7bp';
        $dataType = "json";
        $data = array(
            'amount' =>  $amount,
            'email' => $email,
            'pnr' => $quotationNo,
            'bank_id' => $bankId,
            'charge_id' => $chargesId,
            'currency' => 'LKR',
        );
        $result = $this->makeApiPaymentCall->makeApiCallFormData($method, $url, $dataType, $data);

        $paymentId = $result['paymentId'];
        // $ResponseData = $this->getPaymentResponse($paymentId);
        return response(['status' => 200, 'link_data' => $result]);
    }

    public function getPaymentResponse($paymId)
    {
        $paymentId = $paymId;
        $method = 'POST';
        $url = '/api/payments/getTransactionStatus';
        // $token = '129|Ty0JbDQzmjw5SCqT0xTnTVcCg8J0asTrUQTGT7bp';
        $dataType = 'json';
        $data = array(
            'paymentId' => $paymentId
        );


        while (true) {

            $result = $this->makeApiPaymentCall->makeApiCallFormData($method, $url, $dataType, $data);
            // if ($result) {

            // } else {
            //     break;
            // }
            if ($result['status_code'] == 1) {
                break;
            }


            sleep(5);
        }

        // dd($result);

        // $reciptData = $this->getPaymentRecipt($paymentId);
        return response(['status' => 200, 'data' => $result['status_code']]);
    }


    public function getPaymentRecipt($payId)
    {
        // return $payId['paymentId'];
        $paymentId =   $payId;
        $paymentId = base64_encode(urlencode($paymentId));
        $method = "POST";
        $url = '/api/payments/getReceipt';
        // $token = session()->get('payment_access_token');
        $dataType = "json";
        $data = array(
            'paymentId' => $paymentId
        );

        // return $data;
        $paymentReceipt = $this->makeApiPaymentCall->makeApiCallFormData($method, $url, $dataType, $data);

        return response(['status' => 200, 'data' => $paymentReceipt]);
    }


    /* ################################################################### */
    /* ################################################################### */
    /* ################ Stripe Payment Gateway Area ###################### */
    /* ################################################################### */
    /* ################################################################### */


    public function checkout(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SEC_KEY'));

        // $LineItems = [[
        //     'price' => $request['totalamount']
        // ]];

        try {
            $order = $stripe->products->create(['name' => $request['order_num']]);

            $Price = $stripe->prices->create(
                ['product' => $order->id, 'unit_amount_decimal' => $request['amountPayNow'] * 100, 'currency' => 'usd']
            );

            $session_pay = $stripe->checkout->sessions->create([
                'success_url' => route('checkout.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('checkout.cancel', [], true),
                'line_items' => [
                    [
                        'price' => $Price['id'],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
            ]);

            return response()->json([
                'status' => 200,
                'url' => $session_pay
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SEC_KEY'));
        $sessionId = $request->get('session_id');

        try {

            $isSuccess = false;

            // return $session;

            while (true) {
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session['payment_status'] === 'paid' && $session['status'] === 'complete') {
                    break;
                }
            }


            return response([
                'status' => 200,
                'message' => 'success'
            ]);

            // while (true) {
            //     if ($session['payment_status'] == 'paid' && $session['status'] == 'complete') {


            //         break;
            //     }
            // }

            // return response([
            //     'status' => 200
            // ]);

            // return redirect()->away('https://aahaas.appletechlabs.com/main/orderPaymentStatus');
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }

    public function cancel(Request $request)
    {
        return response([
            'status' => 401,
            'req' => $request
        ]); //redirect()->away('https://aahaas.appletechlabs.com/main/landing');
    }

    /* ########################## */
    public function createCheckout(Request $request)
    {
        CheckoutID::create([
            'checkout_date' => $request['date'],
            'user_id' => $request['userid'],
            'checkout_status' => "CustomerOrdered",
            'total_amount' => $request['totAmount'],
            'paid_amount' => $request['paidTot'],
            'balance_amount' => $request['balanceTot'],
            'payment_type' => $request['paymentMethod'],
            'pay_category' => $request['pay_catgory']
        ]);

        $order_id = DB::table('tbl_checkout_ids')->select('id')->orderBy('id', 'DESC')->first();

        return response([
            'status' => 200,
            'oid' => $order_id->id
        ]);
    }
}
