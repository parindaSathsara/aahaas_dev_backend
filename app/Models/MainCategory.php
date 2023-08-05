<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MainCategory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_maincategory';

    protected $fillable = [
        'maincat_type',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
