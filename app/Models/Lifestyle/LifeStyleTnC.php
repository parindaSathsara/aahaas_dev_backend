<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleTnC extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_terms_and_conditions';

    protected $fillable = [
        'termsncondition_id',
        'lifestyle_id',
        'general_tnc',
        'cancel_policy',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
