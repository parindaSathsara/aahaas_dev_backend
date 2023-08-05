<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\VendorUser\VendorUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SellerController extends Controller
{

    public $seller;
    public $vendor;

    public function __construct()
    {
        $this->seller = new Seller();
        $this->vendor = new VendorUser();
    }

    /* Pull all the seller data function starting */

    public function index()
    {
        $sellersData = DB::table('tbl_seller')->get();

        return response()->json([
            'status' => 200,
            'sellerData' => $sellersData
        ]);
    }

    /* Pull all the seller data function Ending */

    /* Generate seller automatic Id Function starting */

    public function generateSellerAutoId()
    {
        $getSellerId = DB::table('tbl_seller')->limit(1)->orderBy('id', 'DESC')->get('id');

        $sellerIdEncode = json_encode($getSellerId);
        $sellerIdDecode = json_decode($sellerIdEncode, true);

        $currentId = $sellerIdDecode[0]['id'];
        $sellerGenId = 'AHSLR0' . $currentId + 1;

        return response()->json([
            'status' => 200,
            'sellerId' => $sellerGenId
        ]);
    }

    /* Generate seller automatic Id Function Ending */

    /* New Seller creation function starting */
    public function createSeller(Request $request)
    {
        try {

            $seller_code = $request['seller_code'];
            $company_env = $request['company_env'];
            $seller_name = $request['seller_name'];
            $seller_email = $request['seller_email'];
            $company_name = $request['company_name'];
            $company_address = $request['company_address'];
            $br_number = $request['br_number'];
            $nic_no = $request['nic_no'];
            $seller_contact = $request['seller_contact'];
            $company_contact = $request['company_contact'];
            $status = $request['status'];
            $lat_lon = $request['lat_lon'];

            $validator = Validator::make($request->all(), [
                'seller_code' => 'required',
                'seller_name' => 'required',
                // 'seller_email' => 'required',
                'company_name' => 'required',
                'company_address' => 'required',
                'br_number' => 'required',
                'nic_no' => 'required',
                'seller_contact' => 'required',
                'company_contact' => 'required',
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {

                $br_Img_array = array();
                $nic_Img_array = array();
                $profile_Img_array = array();

                //Uploading the seller image document files
                if ($request->has('br_Image') || $request->has('nic_image') || $request->has('profile_image')) {

                    if ($request->file('br_Image')) {

                        $business_img = $request->file('br_Image');

                        foreach ($business_img as $brimg) {
                            $brimagename = $brimg->getClientOriginalName();
                            $brimageextension = $brimg->getClientOriginalExtension();
                            $imagefullname = $brimagename . '.' . $brimageextension;
                            $uploadpath = 'uploads/seller_docs/br_docs';
                            $url = $uploadpath . $imagefullname;
                            $brimg->move($uploadpath, $brimagename);
                            $br_Img_array[] = $url;
                        }
                    }

                    if ($request->file('nic_image')) {

                        $nationalid_img = $request->file('nic_image');

                        foreach ($nationalid_img as $nicimg) {
                            $nationidimg = $nicimg->getClientOriginalName();
                            $nationalidimageextension = $nicimg->getClientOriginalExtension();
                            $imagefullname = $nationidimg . '.' . $nationalidimageextension;
                            $uploadpath = 'uploads/seller_docs/nic_docs';
                            $url = $uploadpath . $imagefullname;
                            $nicimg->move($uploadpath, $nationidimg);
                            $nic_Img_array[] = $url;
                        }
                    }

                    if ($request->file('profile_image')) {

                        $profile_img = $request->file('profile_image');

                        foreach ($profile_img as $profileimg) {
                            $profileimage = $profileimg->getClientOriginalName();
                            $profileimageextension = $profileimg->getClientOriginalExtension();
                            $imagefullname = $profileimage . '.' . $profileimageextension;
                            $uploadpath = 'uploads/seller_docs/seller_images';
                            $url = $uploadpath . $imagefullname;
                            $profileimg->move($uploadpath, $profileimage);
                            $profile_Img_array[] = $url;
                        }
                    }


                    // $file2 = $request->file('nic_image');
                    // $fileExtension2 = $file2->getClientOriginalExtension();
                    // $fileName2 = $request->input('nic_no') . '.' . $fileExtension2;
                    // $file2->move('uploads/seller_docs/nic_docs', $fileName2);
                    // $nicImage = 'uploads/seller_docs/nic_docs/' . $fileName2;

                    // $file3 = $request->file('profile_image');
                    // $fileExtension3 = $file3->getClientOriginalExtension();
                    // $fileName3 = $request->input('seller_code') . '.' . $fileExtension3;
                    // $file3->move('uploads/seller_docs/seller_images', $fileName3);
                    // $sellerProfileImage = 'uploads/seller_docs/seller_images/' . $fileName3;


                    $response = $this->seller->createVendorBusinessProfile($seller_code, $company_env, $seller_name, $seller_email, $company_name, $company_address, $br_number, $nic_no, $seller_contact, $company_contact, $status, $lat_lon, $br_Img_array, $nic_Img_array, $profile_Img_array);

                    return $response;
                } else {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Document Images are required to proceed the session'
                    ]);
                }
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }

    /* New Seller creation function Ending */


    /* New Seller Activation function Starting */
    public function sellerActivation(Request $request, $id)
    {

        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $status = $request->input('status');
            $updated_by = $request->input('updated_by');

            $sellerActivationUpdate = DB::select(DB::raw("UPDATE tbl_seller SET
                                        status='$status',updated_at='$currentTime',updated_by='$updated_by' WHERE seller_code='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'Seller Activated'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* New Seller Activation function Ending */

    /* Get seller full data set by selectd Id function Starting */
    public function getSellerDetailsById($id)
    {
        try {

            $sellerById = DB::table('tbl_seller')->select('*')->where('seller_code', $id)->first();

            return response()->json([
                'status' => 200,
                'seller_code' => $sellerById->seller_code,
                'seller_name' => $sellerById->seller_name,
                'seller_email' => $sellerById->seller_email,
                'company_name' => $sellerById->company_name,
                'company_address' => $sellerById->company_address,
                'br_number' => $sellerById->br_number,
                'br_copyimage' => $sellerById->br_copyimage,
                'nic_no' => $sellerById->nic_no,
                'nic_image' => $sellerById->nic_image,
                'seller_contact' => $sellerById->seller_contact,
                'company_contact' => $sellerById->company_contact,
                'seller_profilepic' => $sellerById->seller_profilepic,
                'status' => $sellerById->status
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* Get seller full data set by selectd Id function Ending */

    //create new seller login
    public function newSellerLoginCreate(Request $request)
    {
        try {

            $last = DB::table('vendor_users')->orderBy('id', 'DESC')->first();
            $id = $last == null ? 1 : $last->id + 1;
            $time = Carbon::now();
            $auto_id = 'SLR00' . $id . '/' . Str::random(5) . $time->milli;

            // return $auto_id;

            $country = $request['country'];
            $loginemail = $request['login_email'];
            $loginpassword = $request['login_password'];
            $userid = $request['user_id'];

            $response = $this->vendor->createSellerLogin($auto_id, $country, $loginemail, $loginpassword, $userid);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //test method
    public function sendEmail(Request $request)
    {
        $response = $this->vendor->sendVerifyEmail($request['email']);

        return $response;
    }

    //code verifiy
    public function verifyCode(Request $request)
    {
        $code = $request['code'];
        $email = $request['email'];

        $response = $this->vendor->verifySecurityCode($code, $email);

        return $response;
    }

    //get registerrd accounts
    public function getUserCount($id)
    {
        try {
            $response = $this->vendor->checkRegisteredUsers($id);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
