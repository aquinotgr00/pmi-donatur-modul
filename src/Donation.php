<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;

use BajakLautMalaka\PmiDonatur\Jobs\SendEmailStatus;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class Donation extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'campaign_id',
        'donator_id', 'amount', 'pick_method',
        'payment_method', 'status', 'guest', 'anonym',
        'image', 'admin_id','invoice_id',
        'address','notes'
    ];

    protected $appends = ['status_text','payment_method_text','pick_method_text'];

    /**
     * Update donation status
     *
     * @param  int $id
     *
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        return $this->where('id', $id)
                    ->update(['status' => $status]);
    }

    public function donator()
    {
        if (class_exists('BajakLautMalaka\PmiDonatur\Donator')) {
            return $this->belongsTo('BajakLautMalaka\PmiDonatur\Donator');
        }
    }

    public function campaign()
    {
        if (class_exists('BajakLautMalaka\PmiDonatur\Campaign'))
            return $this->belongsTo('BajakLautMalaka\PmiDonatur\Campaign');
    }
    
    public function donationItems()
    {
        if (class_exists('BajakLautMalaka\PmiDonatur\DonationItem')) {
            return $this->hasMany('BajakLautMalaka\PmiDonatur\DonationItem');
        }
    }
    
    /**
     * Send email to inform donator/user about their donation status.
     *
     * @param  array  $data
     *
     * @return void
     */
    public function sendEmailStatus($email, $data)
    {
        dispatch(new SendEmailStatus($email, $data));
    }
    
    public function handleDonationImage($image)
    {
        $image_url = null;
        if ($image) {
            $extension  = $image->getClientOriginalExtension();
            $file_name  = $image->getFilename() . '.' . $extension;

            $path = public_path('storage/donation-image/'.$file_name);

            //Resize image here
            $img = Image::make($image)->resize(450, 350, function($constraint) {
                $constraint->aspectRatio();
            });
            
            $img->save($path);
            
            $image_url = url('storage/donation-image/'.$file_name);
        }

        return $image_url;
    }

    public function getStatusTextAttribute()
    {
        $id_status  = $this->status;
        $items      = config('donation.status');
        return (isset($items[$id_status]))? $items[$id_status] : '';
    }

    public function getPaymentMethodTextAttribute()
    {
        
        $id_payment  = $this->payment_method;
        $items       = config('donation.payment_method');
        return (isset($items[$id_payment]))? $items[$id_payment] : '';
        
    }

    public function getPickMethodTextAttribute()
    {
        $id_pick  = $this->pick_method;
        $items    = config('donation.pick_method');
        return (isset($items[$id_pick]))? $items[$id_pick] : '';
    }
}
