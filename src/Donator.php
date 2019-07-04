<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use BajakLautMalaka\PmiDonatur\Jobs\SendEmailRegistration;

class Donator extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'image', 'dob', 'address',
        'province', 'city', 'subdistrict', 'subdivision',
        'postal_code', 'gender', 'verified'
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
    
    /**
     * Sending email and access token to login / verification
     *
     * @param  array  $data
     *
     * @return void
     */
    public function sendEmailAndToken($data)
    {
        dispatch(new SendEmailRegistration($data));
    }
}