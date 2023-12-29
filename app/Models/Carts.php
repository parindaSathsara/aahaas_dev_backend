<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carts extends Model {
    use HasFactory;
    protected $table = 'tbl_carts';

    public function sharedCart() {

        return $this->hasMany( SharedCarts::class, 'cart_id', 'cart_id' );
    }
}
