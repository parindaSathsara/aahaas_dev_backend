<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public $customer;
    public $user;

    public function __construct()
    {
        $this->customer = new Customer();
        $this->user = new User();
    }

    /* Fetch all the customer Data function starting */
    public function index()
    {
        $customerData = DB::table('tbl_customer')->get();

        return response()->json([
            'status' => 200,
            'customerData' => $customerData
        ]);
    }
    /* Fetch all the customer Data function ending */

    /* Generare unique customer id function starting */

    public function updateCustomerProfile(Request $request)
    {

        try {

            $currentTime = \Carbon\Carbon::now()->toDateTimeString();

            $CusName = $request['customername'];
            $CusConNum = $request['contact_number'];
            $CusEmail = $request['customer_email'];
            $CusCountry = $request['country'];
            $CusAddress = $request['customerhomeaddress'];

            $customerID = $request['customerID'];

            // $CusImage = $request['userImage'];

            $validator = Validator::make($request->all(), [
                'customername' => 'required',
                'contact_number' => 'required',
                'country' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                if ($request->hasFile('userImage')) {
                    $file = $request->file('userImage');
                    $fileExtension = $file->getClientOriginalExtension();
                    $fileName = $request->input('customername') . rand(20000, 50000) . '.' . $fileExtension;
                    $file->move('uploads/customer_images/', $fileName);
                    $customerImage = 'uploads/customer_images/' . $fileName;

                    $DB_Query = DB::select(DB::raw("UPDATE tbl_customer SET customer_fname='$CusName',contact_number='$CusConNum',customer_email='$CusEmail',customer_nationality='$CusCountry',customer_profilepic='$customerImage',
                    customer_address='$CusAddress',customer_status='Active',updated_at='$currentTime' WHERE customer_id='$customerID'"));

                    DB::select(DB::raw("UPDATE users SET username='$CusName', email='$CusEmail' WHERE id='$customerID'"));
                } else {
                    $DB_Query = DB::select(DB::raw("UPDATE tbl_customer SET customer_fname='$CusName',contact_number='$CusConNum',customer_email='$CusEmail',customer_nationality='$CusCountry',
                    customer_address='$CusAddress',customer_status='Active',updated_at='$currentTime' WHERE customer_id='$customerID'"));

                    DB::select(DB::raw("UPDATE users SET username='$CusName', email='$CusEmail' WHERE id='$customerID'"));
                }

                return response(['status' => 200, 'updated_data' => $DB_Query]);
            }

            // return $request;
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }

    /* Generare unique customer id function ending */

    // /* New Customer creation function Starting */

    public function registerNewCustomer(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'customername' => 'required',
                'customercontact' => 'required|unique:tbl_customer',
                'customeremail' => 'required|email|unique:tbl_customer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                if ($request->hasFile('userImage')) {
                    $file = $request->file('userImage');
                    $fileExtension = $file->getClientOriginalExtension();
                    $fileName = $request->input('customername') . '.' . $fileExtension;
                    $file->move('uploads/customer_images/', $fileName);
                    $customerImage = 'uploads/customer_images/' . $fileName;
                }

                $currentTime = \Carbon\Carbon::now()->toDateTimeString();

                $customer = Customer::create([
                    'cutomer_id' => $this->generateCustomerId(),
                    'customer_fname' => $request->input('customername'),
                    'contact_number' => $request->input('customercontact'),
                    'customer_email' => $request->input('customeremail'),
                    'customer_nationality' => $request->input('country'),
                    'customer_profilepic' => $customerImage,
                    'customer_address' => $request->input('customerhomeaddress'),
                    'customer_status' => 'Active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                $token = $customer->createToken($customer->customer_email . '_Token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'token' => $token,
                    'cusGenId' => $customer->cutomer_id,
                    'message' => 'Customer Created'
                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    /* New Customer creation function Ending */

    /* Get Count of all the customers Functions starting */

    public function getCustomerCount()
    {
        $cusCount = DB::table('tbl_customer')->get()->count();

        return response()->json([
            'status' => 200,
            'cusCount' => $cusCount
        ]);
    }

    /* Get Count of all the customers Functions Ending */

    public function getCustomerDataById($id)
    {
        $cx_id = $id;

        $cus_Data = DB::table('tbl_customer')->where('customer_id', '=', $cx_id)->first();

        return response([
            'status' => 200,
            'customer_data' => $cus_Data
        ]);
    }


    public function getCustomerDataByOriginId($id)
    {

        $cus_Data = DB::table('tbl_customer')->where('customer_id', '=', $id)->first();

        return response([
            'status' => 200,
            'customer_data' => $cus_Data
        ]);
    }

    //Deactivate customer account
    public function deactivateCustomerAccount($id)
    {
        try {

            $response = $this->customer->deactCustomerAccount($id);
            $response2 = $this->user->deactUserAccount($id);

            return response([
                'status' => 200,
                'res_one' => $response,
                'res_two' => $response2
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
