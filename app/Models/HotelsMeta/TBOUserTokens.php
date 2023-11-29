<?php

namespace App\Models\HotelsMeta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TBOUserTokens extends Model
{
    use HasFactory;

    protected $table = 'tbo_user_tokens';

    // public $timestamps = false;
    protected $fillable = [
        'token_id',
        'token_code',
        'token_ip',
    ];
}
