<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /* Get All User data Function Starting */
    public function index()
    {
        $userData = DB::table('users')->get();

        return response()->json(auth()->user());

        return response()->json([
            'status' => 200,
            'userData' => $userData
        ]);
    }

    /* Get All User data Function Ending */

    /* Get user data by Id function Starting */
    public function getUserById($id)
    {
        try {

            $userById = DB::table('users')->select('*')->where('id', $id)->first();

            // if($userById->isEmpty())
            // {
            //     return response()->json([
            //         'status'=>404,
            //         'message'=>'No Data Found!'
            //     ]);
            // }
            // else
            // {
            //     return response()->json([
            //         'status'=>200,
            //         'user_id'=>$userById->id,
            //         'username'=>$userById->username,
            //         'user_role'=>$userById->user_role,
            //         'created_at'=>$userById->created_at,
            //         'updated_at'=>$userById->updated_at,
            //         'updated_by'=>$userById->updated_by
            //     ]);
            // }

            return response()->json([
                'status' => 200,
                'userData'=>$userById,
                'user_id' => $userById->id,
                'username' => $userById->username,
                'user_role' => $userById->user_role,
                'created_at' => $userById->created_at,
                'updated_at' => $userById->updated_at,
                'updated_by' => $userById->updated_by,
                'user_status' => $userById->user_status
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    /* Get user data by Id function Ending */


    /* User Data Updating Function Starting */
    public function updateUserData(Request $request, $id)
    {
        try {

            // $token = $request->session()->token();
            $token = csrf_token();

            $validator = Validator::make($request->all(), [
                'user_role' => 'required',
                'user_status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_errors' => $validator->messages()
                ]);
            } else {
                $updateUser = User::find($id);

                $updateUser->user_role = $request->input('user_role');
                $updateUser->user_status = $request->input('user_status');

                $updateUser->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'User Data Updated Successfully'
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }

    /* User Data Updating Function Ending */

    /* User Data Deletion Function Starting */
    public function userDeletion($id)
    {
        try {
            $deleteUser = User::find($id);
            $deleteUser->delete();
            return response()->json([
                'status' => 200,
                'message' => 'User Removed from the System Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }

    /* User Data Deletion Function Ending */
}
