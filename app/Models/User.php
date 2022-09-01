<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public const LEVEL_1 = 1;
    public const LEVEL_2 = 2;
    public const LEVEL_8 = 8;
    public const LEVEL_MASTER = 9;
    public const LEVELS = [
        self::LEVEL_1,
        self::LEVEL_2,
        self::LEVEL_8,
        self::LEVEL_MASTER,
    ];

    public const GENDER_NONE = 0;
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;
    public const GENDERS = [
        self::GENDER_NONE,
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'level',
        'gender',
        'photo',
        'email',
        'confirmation_token',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'confirmation_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * User levels
     * @return array
     */
    public static function levels()
    {
        return self::LEVELS;
    }

    /**
     * User genders
     * @return array
     */
    public static function genders()
    {
        return self::GENDERS;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
