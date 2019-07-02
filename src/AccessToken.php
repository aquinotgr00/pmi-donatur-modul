<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccessToken extends Model
{
    protected $table = "access_token";

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'donator_id', 'token', 'expired_at'
    ];

    /**
     * Verification sending token from client
     *
     * @param  mixed $token
     *
     * @return mixed
     */
    public function verifiedToken($token)
    {
        return $this->where('token', $token)->where('expired_at', ">=", Carbon::now())->first();
    }

    /**
     * Expiring token that active for donator client
     *
     * @param mixed $token
     *
     * @return bool
     */
    public function expiringToken($token)
    {
        return $this->where('token', $token)
                    ->where('expired_at', ">=", Carbon::now())
                    ->update(['expired_at'=>Carbon::now()]);
    }
}
