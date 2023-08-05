<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SubMainCategorySub extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_submaincategorysub';

    protected $fillable = [
        'submaincatsub_type',
        'submaincat_id',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
