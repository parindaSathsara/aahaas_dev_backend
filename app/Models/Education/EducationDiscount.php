<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationDiscount extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_discount';

    protected $fillable = [
        'id',
        'edu_id',
        'discount_type',
        'service_location_typeid',
        'edu_inventory_id',
        'edu_rate_id',
        'value',
        'inventory_limit',
        'discountlimit_per_person',
        'discount_per_order',
        'sale_startdate',
        'sale_enddate',
        'user_level',

        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
