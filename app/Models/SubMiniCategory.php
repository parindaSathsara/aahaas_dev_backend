<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SubMiniCategory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_manufacture';

    protected $fillable = [
        'manufacture_title',
        'seller_id',
        'subcategory_id',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
