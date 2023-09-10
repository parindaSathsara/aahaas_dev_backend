<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, $user_id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Invalid Expired URL Provided.'
            ], 401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            // return 'True';
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Email already verified'
            ]);
        }

        return redirect()->to('https://frontend.aahaas.com/user-login');
    }
}
