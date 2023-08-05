<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class OrderFeedback extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_orderfeedback';

    protected $fillable = [
        'order_id',
        'feedback_reason',
        'feedback_title',
        'ref_image',
        'feedback',
        'user_id',
    ];

    public $timestamps = false;
}
