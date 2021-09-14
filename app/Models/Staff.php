<?php

namespace App\Models;

use App\Models\Contracts\Timestampable;
use App\Models\Traits\HasOptionalTimestampsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Staff extends Authenticatable implements JWTSubject, Timestampable
{
    use HasOptionalTimestampsTrait;
    use Notifiable;

    public $incrementing = false;

    protected $table = 'staffs';

    protected $dispatchesEvents = [
        'creating' => \App\Events\Model\CreatingStaff::class,
        'updating' => \App\Events\Model\UpdatingStaff::class,
    ];

    /**
     * キーのカラムが更新されると値のカラムに新しいタイムスタンプが入るように登録する。
     *
     * @var array
     */
    protected $optionalTimestampMap = [
        'token' => 'token_updated_at',
    ];

    protected $fillable = [
        'code',
        'name',
        'token',
        'role',
        'token_limit',
        'token_updated_at',
    ];

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
