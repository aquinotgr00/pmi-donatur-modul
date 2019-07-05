<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;

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
        'payment_method', 'status', 'guest', 'anonym'
    ];

    /**
     * Update donation status
     *
     * @param  int $id
     *
     * @return bool
     */
    public function updateDonationStatus($id, $status)
    {
        return $this->where('id', $id)
                    ->update(['status' => $status]);
    }

    public function donator()
    {
        if (class_exists('Donator')) {
            return $this->belongsTo('Donator');
        }
    }
    
    public function donationItems()
    {
        if (class_exists('DonationItem')) {
            return $this->hasMany('DonationItem');
        }
    }
}
