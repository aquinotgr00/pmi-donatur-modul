<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use BajakLautMalaka\PmiDonatur\Jobs\SendEmailRegistration;
use BajakLautMalaka\PmiDonatur\Jobs\SendEmailDonatorResetPassword;
use BajakLautMalaka\PmiDonatur\Jobs\SendEmailSuccess;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
        'postal_code', 'gender', 'verified', 'user_id'
    ]; 

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
        return $this->hasMany('BajakLautMalaka\PmiDonatur\Donation');
    }

    public function handleDonatorPicture($image)
    {
        $image_url = null;
        if ($image) {
            $extension  = $image->getClientOriginalExtension();
            $file_name  = $image->getFilename() . '.' . $extension;
            $folder     = 'donator-picture';

            Storage::disk('public')->put($folder.'/'.$file_name, File::get($image));

            $image_url = url($folder.'/'.$file_name);
        }

        return $image_url;
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

    /**
     * sending email and access token to Forgot / reset password
     *
     * @param  array  $data
     *
     * @return void
     */
    public function sendEmailAndTokenReset($data)
    {
        dispatch(new SendEmailDonatorResetPassword($data));
    }
    
    /**
     * Sending email that use successfully change their password.
     *
     * @param  array  $data
     *
     * @return void
     */
    public function sendEmailSuccess($data)
    {
        dispatch(new SendEmailSuccess($data));
    }
}