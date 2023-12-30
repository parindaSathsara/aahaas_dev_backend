<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail {
    use HasApiTokens, HasFactory, Notifiable, AuthenticationLoggable;

    protected $table = 'users';

    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'username',
        'email',
        'email_verified_at',
        'password',
        'user_role',
        'created_at',
        'updated_at',
        'updated_by',
        'user_status',
        'user_type',
        'user_platform'
    ];

    /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public $timestamps = false;

    /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //deactivate user account

    public function deactUserAccount( $id ) {
        try {

            DB::table( 'users' )->where( 'id', $id )->update( [ 'user_status'=> 'Deactivate' ] );

        } catch ( \Throwable $th ) {
            throw $th;
        }
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
