<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedCarts extends Model {
    use HasFactory;
    protected $table = 'shared_carts';

    public function cart() {
        return $this->belongsTo( Carts::class, 'cart_id', 'cart_id' );
    }
}
