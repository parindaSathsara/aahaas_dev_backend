<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class DiscountType extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_discount_types';

    protected $fillable = [
        'discount_title_type',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
