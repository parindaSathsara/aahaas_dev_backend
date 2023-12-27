<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carts;
use App\Models\SharedCarts;
use App\Models\User;

class CartShareController extends Controller {
    // public function index() {
    //     return response()->json( [ 'status'=>'success', 'messege'=>'Success', 'data'=>'test' ] );
    // }

    public function share_cart( Request $request, $cart_id ) {

        $find_user = User::select( 'id' )->where( 'email', $request->email )->first();
        if ( !$find_user ) {
            return response()->json( [ 'status'=>'fail', 'messege'=>'Unable to find a user with the provided email' ] );
        }

        $find_cart = SharedCarts::where( 'cart_id', $cart_id )->where( 'customer_id', $find_user->id )->first();
        if ( $find_cart ) {
            return response()->json( [ 'status'=>'fail', 'messege'=>'This cart is already shered with this user' ] );
        }

        $share_cart = new SharedCarts();
        $share_cart->cart_id = $cart_id;
        $share_cart->customer_id = $find_user->id;
        $share_cart->save();

        return response()->json( [ 'status'=>'success', 'messege'=>'Your cart is shared' ] );
    }

    public function get_shared_carts( $user_id ) {

        $carts = SharedCarts::with( 'cart' )->where( 'customer_id', $user_id )->get();

        return response()->json( [ 'status'=>'success', 'messege'=>'Success', 'data'=>$carts ] );
    }
}
