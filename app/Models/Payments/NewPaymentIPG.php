<?php

namespace App\Models\Payments;

use App\Models\Customer\MainCheckout;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewPaymentIPG extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    public $APIUsername;
    public $APIPassword;
    public $MerchantId;
    public $Transaction;
    public $PaymentIPG;

    public function __construct()
    {
        $this->APIUsername = 'merchant.TESTAPPLEHOLILKR';
        $this->APIPassword = '2647d21e46251c604f5acb25db01bb7a';
        $this->MerchantId = 'TESTAPPLEHOLILKR';
        $this->Transaction = new Transaction();
        $this->PaymentIPG = new PaymentIPG();
    }



    public function createNewPaymentMobile($orderid, $orderamount, $cxemail)
    {
        $CheckoutArray = [];

        $CheckoutArray['apiOperation'] = "INITIATE_CHECKOUT";
        $CheckoutArray['interaction']['merchant']['name'] = 'Apple Holidays';
        $CheckoutArray['interaction']['operation'] = "PURCHASE";
        $CheckoutArray['interaction']['returnUrl'] = "https://aahaas.appletechlabs.com/checkout-order-payment-status-mobile/" . $orderid;
        // $CheckoutArray['interaction']['returnUrl'] = "http://192.168.4.117:8000/checkout-order-payment-status-mobile/" . $orderid;

        $CheckoutArray['interaction']['displayControl']['billingAddress'] = 'HIDE';
        $CheckoutArray['interaction']['displayControl']['customerEmail'] = "HIDE";
        $CheckoutArray['interaction']['displayControl']['shipping'] = "HIDE";
        $CheckoutArray['order']['amount'] = $orderamount;
        $CheckoutArray['order']['currency'] = "LKR";
        $CheckoutArray['order']['description'] = 'Payment for ' . $orderid;
        $CheckoutArray['order']['id'] = $orderid;

        $API_Response = Http::withBasicAuth($this->APIUsername, $this->APIPassword)->post('https://cbcmpgs.gateway.mastercard.com/api/rest/version/67/merchant/' . $this->MerchantId . '/session', $CheckoutArray)->json();

        // return $API_Response;

        $this->PaymentIPG->createNewPaymentIpgRow($orderid, $orderamount, $cxemail);

        $sessionId = $API_Response['session']['id'];
        $version = $API_Response['session']['version'];


        return response([
            'status' => 200,
            'session_id' => $sessionId,
            'version' => $version
        ]);
    }

    //create aahaas payment
    public function createNewPayment($orderid, $orderamount, $cxemail, $currency_type)
    {
        $CheckoutArray = [];

        $Rate = DB::table('table_currency')->where(['base' => 'LKR', 'to_currency' => $currency_type])->first();

        $Amount = (float)$orderamount / (float)$Rate->rate;

        $FinalAmount = number_format((float)$Amount, 2, '.', '');

        // return $FinalAmount;

        $CheckoutArray['apiOperation'] = "INITIATE_CHECKOUT";
        $CheckoutArray['interaction']['merchant']['name'] = 'Apple Holidays';
        $CheckoutArray['interaction']['operation'] = "PURCHASE";
        $CheckoutArray['interaction']['returnUrl'] = "https://frontend.aahaas.com/main/checkout-order-payment-status/" . $orderid;
        // $CheckoutArray['interaction']['returnUrl'] = "http://192.168.4.117:8000/checkout-order-payment-status-mobile/" . $orderid;
        $CheckoutArray['interaction']['displayControl']['billingAddress'] = 'HIDE';
        $CheckoutArray['interaction']['displayControl']['customerEmail'] = "HIDE";
        $CheckoutArray['interaction']['displayControl']['shipping'] = "HIDE";
        $CheckoutArray['order']['amount'] = $FinalAmount;
        $CheckoutArray['order']['currency'] = "LKR";
        $CheckoutArray['order']['description'] = 'Payment for ' . $orderid;
        $CheckoutArray['order']['id'] = $orderid;

        $API_Response = Http::withBasicAuth($this->APIUsername, $this->APIPassword)->post('https://cbcmpgs.gateway.mastercard.com/api/rest/version/67/merchant/' . $this->MerchantId . '/session', $CheckoutArray)->json();

        // return $API_Response;

        $this->PaymentIPG->createNewPaymentIpgRow($orderid, $FinalAmount, $cxemail);

        $sessionId = $API_Response['session']['id'];
        $version = $API_Response['session']['version'];


        return response([
            'status' => 200,
            'session_id' => $sessionId,
            'version' => $version
        ]);
    }

    //get payment status response
    public function getPaymentResponse($orderid)
    {
        try {

            $Url = 'https://cbcmpgs.gateway.mastercard.com/api/rest/version/67/merchant/' . $this->MerchantId . '/order/' . $orderid;

            $API_response = Http::withBasicAuth($this->APIUsername, $this->APIPassword)->get($Url)->json();

            // return $API_response;

            // $AuthToken = $API_response['transaction'][0]['transaction']['receipt'];
            // $TransToken = $API_response['transaction']['acquirer']['transactionId'];
            // $AuthStatus = $API_response['authenticationStatus'];
            $Result = $API_response['result'];
            $Status = $API_response['status'];
            $UserIp = $API_response['device']['ipAddress'];

            $Validator =  $API_response['sourceOfFunds']['type'];

            if ($Validator === 'UNION_PAY') {

                Transaction::create([
                    'pay_id' => $orderid,
                    'auth_token' => $API_response['transaction'][0]['transaction']['receipt'],
                    'trans_token' => $API_response['transaction'][0]['transaction']['acquirer']['transactionId'],
                    'auth_status' => $API_response['transaction'][0]['browserPayment']['interaction']['status'],
                    'result' => $Result . '.' . $Validator,
                    'payment_status' => $Status,
                    'user_ip' => $UserIp,
                ]);
            } else {
                Transaction::create([
                    'pay_id' => $orderid,
                    'auth_token' => $API_response['transaction'][0]['authentication']['3ds']['authenticationToken'],
                    'trans_token' => $API_response['transaction'][0]['transaction']['id'],
                    'auth_status' => $API_response['authenticationStatus'],
                    'result' => $Result,
                    'payment_status' => $Status,
                    'user_ip' => $UserIp,
                ]);
            }


            $SqlQuery = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', $orderid)
                ->join('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                // ->join('payment_amounts', 'payments.id', '=', 'payment_amounts.payment_id')
                // ->join('pending_payments', 'payments.id', '=', 'pending_payments.payment_id')
                // ->join('transactions', 'payments.id', '=', 'transactions.payment_id')
                ->first();


            if ($Status === 'CAPTURED') {
                return response([
                    'status' => 200,
                    'data_response' => $API_response,
                    'data_res' => $SqlQuery
                ]);
            } else if ($Status === 'FAILED') {
                return response([
                    'status' => 500,
                    'data_response' => $API_response,
                    'data_res' => $SqlQuery
                ]);
            } else {
                return response([
                    'status' => 400,
                    'data_response' => $API_response,
                    'data_res' => $SqlQuery
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
