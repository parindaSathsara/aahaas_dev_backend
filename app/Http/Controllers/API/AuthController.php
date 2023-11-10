<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\UserVerification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerCarts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use stdClass;
use Stevebauman\Location\Facades\Location;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }
    /* New User Registration Function Starting */
    public function registerUser(Request $request)
    {
        try {

            $userEmail = $request->input('email');
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                $currentTime = \Carbon\Carbon::now()->toDateTimeString();

                // $existUser = DB::table('users')->select('username', 'email')->first();
                try {
                    $newUser = User::create([
                        'username' => $request->input('username'),
                        'email' => $request->input('email'),
                        'password' => Hash::make($request->input('password')),
                        'user_role' => 'User',
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                        'updated_by' => $request->input('email'),
                        'user_status' => 'Active',
                        'user_type' => 'Internal',
                        'user_platform' => 'Internal'
                    ]);

                    Mail::mailer('smtp')->to($newUser->email)->send(new UserVerification($newUser));

                    $currentTime = \Carbon\Carbon::now()->toDateTimeString();
                    $token = $newUser->createToken($newUser->email . '_Token')->plainTextToken;
                } catch (\Exception $ex) {
                    // $newUser->delete();
                    throw $ex;
                }

                $newCx = Customer::create([
                    'customer_id' => $newUser->id,
                    'customer_fname' => $newUser->username,
                    'contact_number' => '-',
                    'customer_email' => $newUser->email,
                    'customer_nationality' => '-',
                    'customer_profilepic' => '-',
                    'customer_status' => 'Active',
                    'customer_address' => '-',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                return response()->json([
                    'status' => 200,
                    'token' => $token,
                    'newcx' => $newCx,
                    'message' => 'Registration Success, Verification email sent. Please verify your email to login'
                ]);
                // if ($existUsername == $request->input('username') || $existEmail == $request->input('email')) {
                //     return response()->json([
                //         'status' => 400,
                //         'error_message' => 'Username or Email already Exisits!'
                //     ]);
                // } else {

                // }
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* New User Registration Function Ending */


    /* Common user login function starting */

    public function userLoginWeb(Request $request)
    {
        try {

            // $request->authenticate();

            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ]);

            $input = $request->all();

            $credentials = $request->only('email', 'password');
            // $this->checkTooManyFailedAttempts();
            $user_email = $request->input('email');

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_errors' => $validator->messages()
                ]);
            } else {
                $user = User::where('email', '=', $user_email)->orWhere('username', '=', $user_email)->first();


                if (!$user || !Hash::check($request->input('password'), $user->password)) {
                    // $this->checkTooManyFailedAttempts();
                    $attemps = RateLimiter::hit($this->throttleKey(), $seconds = 3600);

                    $attempts = session()->get('login.attempts', 1); // get attempts, default: 0
                    session()->put('login.attempts', $attempts + 1);

                    if ($attemps >= 20) {
                        return response()->json([
                            'status' => 403,
                            'error_message' => 'Too many attemps',
                            'limit' => $attemps
                        ]);
                    } else {
                        return response()->json([
                            'status' => 404,
                            'error_message' => 'Invalid credentials',
                            'limit' => $attemps
                        ]);
                    }
                } else if ($user->email_verified_at == null) {

                    $attemps = RateLimiter::hit($this->throttleKey(), $seconds = 3600);

                    $attempts = session()->get('login.attempts', 1); // get attempts, default: 0
                    session()->put('login.attempts', $attempts + 1);

                    return response()->json([
                        'status' => 401,
                        'error_message' => 'Please verify your email first',
                        'limit' => $attemps
                    ]);
                } else if (!Hash::check($request->input('password'), $user->password, [])) {
                    throw new Exception('Error occured while logging in.');
                }
                // ->put('user',$user)
                else {

                    $user->tokens()->delete();
                    $token = $user->createToken($user->email . '_Token')->plainTextToken;

                    RateLimiter::clear($this->throttleKey());

                    session()->forget('login.attempts');

                    Session::put('user', $user_email);
                    Session::save();


                    $encryptCookie = Crypt::encrypt($user_email);
                    $encryptUserId = Crypt::encrypt($user->id);

                    RateLimiter::resetAttempts($this->throttleKey());

                    return response(['status' => 200, 'access_Token' => $token, 'cookie' => $encryptCookie, 'user_Id' => $encryptUserId, 'id' => $user->id]);
                }
                // $request->session()->regenerate();
                $request->session()->put('user', $user);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }

    /* Common user login function Ending */

    /* Common login for google users Fucntion Starting */

    public function googleUserDataCheck($request)
    {

        // return $request;
        try {

            $googleUserData = User::where('email', '=', $request)->count();

            $user = User::where('email', '=', $request)->first();
            // return $user;

            if ($googleUserData > 0) {
                $user->tokens()->delete();
                Session::put('useremail', $request);

                $token = $user->createToken($request . '_google_Token')->plainTextToken;

                Session::put('user', $user->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($user->email);
                $encryptUserId = Crypt::encrypt($user->id);

                return response()->json([
                    'status' => 200,
                    'usercount' => $googleUserData,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                ]);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }

    /* Common login for google users Fucntion Ending */


    /* Google User Authenication Function Starting */
    public function getGoogleUserData(Request $request)
    {
        try {
            $currentTime = \Carbon\Carbon::now()->toDateTimeString();
            $email = $request->input('email');

            // return $email;

            $googleUserData = User::where('email', '=', $email)->count();
            $user = User::where('email', '=', $email)->first();

            // return $googleUserData;

            if ($googleUserData > 0) {
                $user->tokens()->delete();
                Session::put('useremail', $email);

                $token = $user->createToken($email . '_google_Token')->plainTextToken;

                Session::put('user', $email);
                Session::save();


                $encryptCookie = Crypt::encrypt($user->email);
                $encryptUserId = Crypt::encrypt($user->id);

                return response([
                    'status' => 200,
                    'usercount' => $googleUserData,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'id' => $user->id
                ]);
            } else {
                $newUser = User::create([
                    'username' => substr($email, 0, 5) . rand(1000, 9999),
                    'email' => $request->input('email'),
                    'password' => 'googledefault',
                    'user_role' => 'User',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => $request->input('email'),
                    'user_status' => 'Active',
                    'user_type' => $request->input('user_type'),
                    'user_platform' => $request->input('user_platform')
                ]);

                $usr_Id = $newUser->id;

                Customer::create([
                    'customer_id' => $usr_Id,
                    'customer_fname' => $newUser->username,
                    'contact_number' => $request->input('contact_number'),
                    'customer_email' => $newUser->email,
                    'customer_nationality' => '-',
                    'customer_profilepic' => $request->input('customer_profilepice'),
                    'customer_status' => 'Active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                CustomerCarts::create([
                    'customer_id' => $usr_Id,
                    'cart_title' => 'My Cart'
                ]);

                $token = $newUser->createToken($newUser->email . '_google_Token')->plainTextToken;

                Session::put('user', $newUser->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($newUser->email);
                $encryptUserId = Crypt::encrypt($newUser->id);

                return response()->json([
                    'status' => 201,
                    'username' => $newUser->username,
                    'email' => $newUser->email,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'usercount' => 0,
                    'id' => $usr_Id,
                    'message' => 'New User Successfully Added to the System'
                ]);

                $request->session()->put('user', $newUser);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }
    /* Google User Authenication Function Ending */

    /* Common login for google users Fucntion Starting */

    public function facebookUserDataCheck($request)
    {

        // return $request;
        try {

            $facebookUserData = User::where('email', '=', $request)->count();

            $user = User::where('email', '=', $request)->first();
            // return $user;

            if ($facebookUserData > 0) {
                $user->tokens()->delete();
                $token = $user->createToken($request . '_facebook_Token')->plainTextToken;
                Session::put('useremail', $request);
                Session::put('user', $user->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($user->email);
                $encryptUserId = Crypt::encrypt($user->id);

                return response()->json([
                    'status' => 200,
                    'usercount' => $facebookUserData,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                ]);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }

    /* Common login for google users Fucntion Ending */

    /* Facebook User Authenication Function Starting */
    public function getFacebookUserData(Request $request)
    {

        try {

            $currentTime = \Carbon\Carbon::now()->toDateTimeString();
            $email = $request['username'];

            $facebookUserData = User::where('username', '=', $email)->count();
            $user = User::where('username', '=', $email)->first();

            if ($facebookUserData > 0) {
                $user->tokens()->delete();
                $token = $user->createToken($email . '_facebook_Token')->plainTextToken;
                Session::put('useremail', $email);
                Session::put('user', $user->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($user->email);
                $encryptUserId = Crypt::encrypt($user->id);

                return response()->json([
                    'status' => 200,
                    'usercount' => $facebookUserData,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'id' => $user->id
                ]);
            } else {
                $newUser = User::create([
                    'username' => $request->input('username'),
                    'email' => $request->input('username'),
                    'password' => 'facebookdefault',
                    'user_role' => 'User',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => $request->input('email'),
                    'user_status' => 'Active',
                    'user_type' => $request->input('user_type'),
                    'user_platform' => $request->input('user_platform')
                ]);

                $usr_Id = $newUser->id;

                Customer::create([
                    'customer_id' => $usr_Id,
                    'customer_fname' => $newUser->email,
                    'contact_number' => $request->input('contact_number'),
                    'customer_email' => $newUser->email,
                    'customer_nationality' => '-',
                    'customer_profilepic' => $request->input('customer_profilepice'),
                    'customer_status' => 'Active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                CustomerCarts::create([
                    'customer_id' => $usr_Id,
                    'cart_title' => 'My Cart'
                ]);

                $token = $newUser->createToken($newUser->email . '_facebook_Token')->plainTextToken;

                Session::put('user', $newUser->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($newUser->email);
                $encryptUserId = Crypt::encrypt($newUser->id);

                return response()->json([
                    'status' => 201,
                    'username' => $newUser->username,
                    'email' => $newUser->email,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'usercount' => 0,
                    'id' => $usr_Id,
                    'message' => 'New User Successfully Added to the System'
                ]);

                $request->session()->put('user', $newUser);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }






    public function getAppleUserData(Request $request)
    {

        try {

            $currentTime = \Carbon\Carbon::now()->toDateTimeString();
            $email = $request['username'];

            $facebookUserData = User::where('username', '=', $email)->count();
            $user = User::where('username', '=', $email)->first();

            if ($facebookUserData > 0) {
                $user->tokens()->delete();
                $token = $user->createToken($email . '_facebook_Token')->plainTextToken;
                Session::put('useremail', $email);
                Session::put('user', $user->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($user->email);
                $encryptUserId = Crypt::encrypt($user->id);

                return response()->json([
                    'status' => 200,
                    'usercount' => $facebookUserData,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'id' => $user->id
                ]);
            } else {
                $newUser = User::create([
                    'username' => $request->input('username'),
                    'email' => $request->input('username'),
                    'password' => 'facebookdefault',
                    'user_role' => 'User',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => $request->input('email'),
                    'user_status' => 'Active',
                    'user_type' => $request->input('user_type'),
                    'user_platform' => $request->input('user_platform')
                ]);

                $usr_Id = $newUser->id;

                Customer::create([
                    'customer_id' => $usr_Id,
                    'customer_fname' => $newUser->email,
                    'contact_number' => $request->input('contact_number'),
                    'customer_email' => $newUser->email,
                    'customer_nationality' => '-',
                    'customer_profilepic' => $request->input('customer_profilepice'),
                    'customer_status' => 'Active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                CustomerCarts::create([
                    'customer_id' => $usr_Id,
                    'cart_title' => 'My Cart'
                ]);

                $token = $newUser->createToken($newUser->email . '_facebook_Token')->plainTextToken;

                Session::put('user', $newUser->email);
                Session::save();


                $encryptCookie = Crypt::encrypt($newUser->email);
                $encryptUserId = Crypt::encrypt($newUser->id);

                return response()->json([
                    'status' => 201,
                    'username' => $newUser->username,
                    'email' => $newUser->email,
                    'token' => $token,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'usercount' => 0,
                    'id' => $usr_Id,
                    'message' => 'New User Successfully Added to the System'
                ]);

                $request->session()->put('user', $newUser);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }












    /* Facebook User Authenication Function Ending */

    public function mobileUserCreation(Request $request)
    {
        try {
            $currentTime = \Carbon\Carbon::now()->toDateTimeString();
            $MobileNumber = $request['mobileNum'];

            $userRowCount = DB::table('users')->where('username', $MobileNumber)->count();

            $newCus = DB::table('tbl_customer')->where('contact_number', $MobileNumber)->count();

            // return $user;

            $user = User::where('username', '=', $MobileNumber)->first();

            if ($userRowCount > 0 || $newCus > 0) {

                $token = $user->createToken($MobileNumber . '_mobile_Token')->plainTextToken;

                // return $token;

                Session::put('user', $MobileNumber);
                Session::save();


                $encryptCookie = Crypt::encrypt($MobileNumber);
                $encryptUserId = Crypt::encrypt($user->id);

                return response()->json([
                    'status' => 200,
                    'usercount' => 'User already exisitng',
                    'token' => $token,
                    'user_count' => 1,
                    'cookie' => $encryptCookie,
                    'user_Id' => $encryptUserId,
                    'id' => $user->id
                ]);
            } else {
                $newUser = User::create([
                    'username' => $MobileNumber,
                    'email' => $MobileNumber,
                    'email_verified_at' => $currentTime,
                    'password' => $MobileNumber,
                    'user_role' => 'User',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => $MobileNumber,
                    'user_status' => 'Active',
                    'user_type' => 'Mobile',
                    'user_platform' => 'Mobile'
                ]);



                $token = $newUser->createToken($MobileNumber . '_mobile_Token')->plainTextToken;

                Session::put('user', $newUser);
                Session::save();


                $encryptCookie = Crypt::encrypt($newUser->username);
                $encryptUserId = Crypt::encrypt($newUser->id);

                $usrId = $newUser->id;

                Customer::create([
                    'customer_id' => $usrId,
                    'customer_fname' => $MobileNumber,
                    'contact_number' => $MobileNumber,
                    'customer_email' => '-',
                    'customer_nationality' => '-',
                    'customer_profilepic' => '-',
                    'customer_status' => 'Active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                CustomerCarts::create([
                    'customer_id' => $usrId,
                    'cart_title' => 'My Cart'
                ]);
            }

            Session::put('user', $MobileNumber);

            return response()->json([
                'status' => 200,
                'message' => 'Mobile Login success',
                'token' => $token,
                'cookie' => $encryptCookie,
                'user_Id' => $encryptUserId,
                'user_count' => 0,
                'id' => $usrId
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }

    // Onetime Password update and verification email send
    public function oneTimePasswordUpdate(Request $request)
    {
        try {

            $UserId = $request['userid'];
            $UserEmail = $request['useremail'];
            $Source = $request['source'];

            if ($Source === 'emailuser') {

                $User = User::where('id', $UserId)->first();

                $User->password = Hash::make($request->input('onetimepass'));
                $User->save();

                Mail::mailer('smtp')->to($User->email)->send(new UserVerification($User));
            } else {
                $User = User::where('id', $UserId)->first();

                $User->password = Hash::make($request->input('onetimepass'));
                $User->save();
            }


            return response([
                'status' => 200,
                'message' => 'Success'
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }


    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        $throteKy = Str::lower(request()->ip());
        return $throteKy;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     */
    public function checkTooManyFailedAttempts()
    {
        $userIPRange = $this->throttleKey();
        // RateLimiter::resetAttempts($userIPRange);
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return true;
        } else {
            return response()->json([
                'status' => 500,
                'error_message' => 'IP address banned. Too many login attempts.'
            ]);
        }
    }

    public function logout(Request $request)
    {
        // $request->user()->tokens()->delete();
        // $request->session()->flush();
        $request->session()->flush();
        Auth::logout();

        return response(['status' => 200, 'message' => 'logout success']);
    }

    // public function verifyMail(){
    //     return view('Mails.Verify');
    // }

    public function getUserCurrentLocation(Request $req)
    {
        $ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
        // $ip = '192.168.4.101';
        // $ip = request()->getClientIp();
        // $ip = '103.227.247.202';pos
        $currentUser = Location::get('fe80::d6b7:e157:5b63:2bc5%17');

        return request()->server();
    }

    public function getCurrentUserById($id)
    {
        $user__Id = $id;
        $UserData = DB::table('users')->join('tbl_customer', 'users.id', '=', 'tbl_customer.customer_id')->where('users.id', $user__Id)->first();

        // return $UserData;
        return response([
            'status' => 200,
            'user_username' => $UserData->username,
            'user_email' => $UserData->customer_email,
            'cxid' => $UserData->customer_id,
            'pro_pic' => $UserData->customer_profilepic
        ]);
    }
}
