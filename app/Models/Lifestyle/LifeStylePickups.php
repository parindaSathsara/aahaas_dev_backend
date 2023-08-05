<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStylePickups extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_pickups';

    protected $fillable = [
        'lifestyle_pickup_id',
        'lifestyle_id',
        'lifestyle_pickup_point',
        'longitude',
        'latitude',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
