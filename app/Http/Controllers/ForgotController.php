<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ForgotRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ResetRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Mail\DemoMail;

class ForgotController extends Controller
{
    /* Getting the User Email to send the Password Reset Link to reset the password function starting */

    public function forgotPassword(Request $request)
    {

        // return 'Response Coming';
        $userEmail = $request->input('userEmail');

        if (User::where('email', $userEmail)->doesntExist()) {
            return response()->json([
                'status' => 404,
                'message' => 'User not Exists, Please Check Again !'
            ]);
        }

        $token = Str::random(10);

        try {

            DB::table('password_resets')->insert([
                'email' => $userEmail,
                'token' => $token
            ]);

            //Sending the password reset link to the user registered email
            Mail::send('Mails.Forgot', ['token' => $token], function ($message) use ($userEmail) {
                $message->to($userEmail);
                $message->subject('Reset your Password');
            });

            return response()->json([
                'status' => 200,
                'email' => $userEmail,
                'message' => 'Password Reset Link Sent Successfully'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }


    }


    public function forgotPasswordMobile(Request $request)
    {

        // return 'Response Coming';
        $userEmail = $request->input('userEmail');

        if (User::where('email', $userEmail)->doesntExist()) {
            return response()->json([
                'status' => 404,
                'message' => 'User not Exists, Please Check Again !'
            ]);
        }

        // $token = Str::random(6);

        $token = rand(333333,999999);



        try {

            DB::table('password_resets')->insert([
                'email' => $userEmail,
                'token' => $token
            ]);

            //Sending the password reset link to the user registered email
            Mail::send('Mails.ForgotMobile', ['token' => $token], function ($message) use ($userEmail) {
                $message->to($userEmail);
                $message->subject('Reset your Password');
            });

            return response()->json([
                'status' => 200,
                'email' => $userEmail,
                'message' => 'Password Reset Link Sent Successfully'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }


    }

    /* Getting the User Email to send the Password Reset Link to reset the password function Ending */

    //** @var User $user */

    /* Create a unique token id to reset the password for each user by User Email Function Starting */

    public function resetPassword(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
                'token'=>'required',
                'userPassword'=>'required',
                'userConfirmPassword'=>'required|same:userPassword'
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>401,
                    'validation_error'=>$validator->messages()
                ]);
            }
            else
            {
                $token = $request['token'];

                if(!$passwordReset = DB::table('password_resets')->where('token',$token)->first())
                {
                    return response()->json([
                        'status'=>400,
                        'message'=>'Invalid Token'
                    ]);
                }

                if(!$user = User::where('email', $passwordReset->email)->first())
                {
                    return response()->json([
                        'status'=>404,
                        'message'=>'User not Exists, Please Try Again !'
                    ]);
                }

                $user->password = Hash::make($request->input('userPassword'));
                $user->save();

                return response()->json([
                    'status'=>200,
                    'user'=>$user,
                    'message'=>'Password Reset Successfully'
                ]);
            }

        }catch(\Exception $exception){

            return response()->json([
                'status'=>401,
                'message'=> throw $exception
            ]);

        }
    }

    /* Create a unique token id to reset the password for each user by User Email Function Ending */

}
