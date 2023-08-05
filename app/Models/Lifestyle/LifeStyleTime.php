<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleTime extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_time';

    protected $fillable = [
        'lifestyle_time_id',
        'lifestyle_id',
        'pickup_id',
        'adult_count',
        'child_count',
        'starting',
        'ending',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
