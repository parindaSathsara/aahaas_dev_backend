<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carts;

class CartShareController extends Controller {
    public function index() {
        return response()->json( [ 'status'=>200, 'messege'=>'Success', 'data'=>'asdasd' ] );
    }
}
