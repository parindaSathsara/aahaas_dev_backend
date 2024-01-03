<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carts;
use App\Models\SharedCarts;
use App\Models\User;

class CartShareController extends Controller {

    public function share_cart( Request $request, $cart_id ) {

        $find_user = User::select( 'id' )->where( 'email', $request->email )->first();
        if ( !$find_user ) {
            return response()->json( [ 'status'=>'fail', 'messege'=>'Unable to find a user with the provided email' ] );
        }

        $self = User::where( 'id', '!=', auth()->user()->id );
        if ( $self ) {
            return response()->json( [ 'status'=>'fail', 'messege'=>'You cant share your own carts with yourself' ] );
        }

        $find_cart_by_user = SharedCarts::where( 'cart_id', $cart_id )->where( 'customer_id', $find_user->id )->first();
        if ( $find_cart_by_user ) {
            if ( $find_cart_by_user->status == 'Pending' ) {
                return response()->json( [ 'status'=>'fail', 'messege'=>'Already send reqest for this cart' ] );
            }
            return response()->json( [ 'status'=>'fail', 'messege'=>'This cart is already shered with this user' ] );
        }

        $share_cart = new SharedCarts();
        $share_cart->cart_id = $cart_id;
        $share_cart->customer_id = $find_user->id;
        $share_cart->status = 'Pending';
        $share_cart->save();

        return response()->json( [ 'status'=>'success', 'messege'=>'Cart share reqest is sent' ] );
    }

    public function accept_cart ( $shared_id ) {

        $cart = SharedCarts::findOrFail( $shared_id );
        $cart->status = 'Shared';
        $cart->save();

        return response()->json( [ 'status'=>'success', 'messege'=>'Cart is accepted' ] );
    }

    public function decline_cart ( $shared_id ) {

        SharedCarts::where( 'id', $shared_id )->where( 'status', 'Pending' )->delete();

        return response()->json( [ 'status'=>'success', 'messege'=>'Cart is decline' ] );
    }

    public function get_incoming_carts( $user_id, $type ) {

        $carts = SharedCarts::select(
            'tbl_carts.cart_id',
            'shared_carts.id as shared_cart_id',
            'tbl_carts.cart_title',
            'users.email as shared_email',
        )
        ->join( 'tbl_carts', 'tbl_carts.cart_id', '=', 'shared_carts.cart_id' )
        ->join( 'users', 'users.id', '=', 'tbl_carts.customer_id' )
        ->where( 'shared_carts.customer_id', $user_id )
        ->where( 'shared_carts.status', $type )
        ->get();

        return response()->json( [ 'status'=>'success', 'messege'=>'Success', 'data'=>$carts ] );
    }

    public function get_self_sharing_carts( $auth_user_id, $type ) {

        $carts = Carts::select(
            'tbl_carts.cart_id',
            'shared_carts.id as shared_cart_id',
            'tbl_carts.cart_title',
            'users.email as shared_email',
        )
        ->join( 'shared_carts', 'shared_carts.cart_id', '=', 'tbl_carts.cart_id' )
        ->join( 'users', 'users.id', '=', 'shared_carts.customer_id' )
        ->where( 'tbl_carts.customer_id', $auth_user_id )
        ->where( 'shared_carts.status', $type )
        ->get();

        return response()->json( [ 'status'=>'success', 'messege'=>'Success', 'data'=>$carts ] );
    }

    public function cancel_cart_request( $shared_id ) {
        SharedCarts::where( 'id', $shared_id )->where( 'status', 'Pending' )->delete();
        return response()->json( [ 'status'=>'success', 'messege'=>'Reqest is canceled' ] );
    }

    public function stop_shared_cart( $shared_id ) {
        SharedCarts::findOrFail( $shared_id )->delete();
        return response()->json( [ 'status'=>'success', 'messege'=>'Cart sharing is stoped' ] );
    }

}
