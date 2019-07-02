<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Donator extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'gender',
        'dob', 'subdistrict', 'city', 'username',
        'verified', 'postal_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * find user by username or email using passport
     *
     * @param  mixed $identifier
     *
     * @return mixed
     */
    public function findForPassport($identifier)
    {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->first();
    }    

    /**
     * Get the member's name.
     *
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    /**
     * Get the member's address.
     *
     * @param  string  $value
     * @return string
     */
    public function getAddressAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Update donator verified status
     *
     * @param  int $id
     *
     * @return bool
     */
    public function verifyDonatur($id)
    {
        return $this->where('id', $id)
                    ->update(['verified' => true]);
    }

    public function donations()
    {
        if (class_exists('Donation')) {
            return $this->hasMany('Donation');
        }
    }
}