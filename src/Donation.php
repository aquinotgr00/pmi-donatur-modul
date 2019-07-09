<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;

use BajakLautMalaka\PmiDonatur\Jobs\SendEmailStatus;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
        'image', 'category', 'admin_id'
    ];

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

            Storage::disk('public')->put('donation-image/'.$file_name, File::get($image));

            $image_url = url('donation-image/' . $file_name);
        }

        return $image_url;
    }
}
