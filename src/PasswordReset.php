<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = "password_resets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'token', 'email', 'created_at' ];
}