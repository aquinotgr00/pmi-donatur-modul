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
        'campaign_id', 'donator_id', 'volunteer_id', 'amount',
        'description', 'pick_method', 'status'
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
    
    /**
     * Get the donation donator's name.
     *
     * @param  string  $value
     * @return string
     */
    public function getDonatorNameAttribute($value)
    {
        return ucwords($value->donator->name);
    }

    public function donator()
    {
        if (class_exists('Donator')) {
            return $this->belongsTo('Donator');
        }
    }
    
    public function volunteer()
    {
        if (class_exists('BajakLautMalaka\pmi-relawan\Volunteer')) {
            return $this->belongsTo('BajakLautMalaka\pmi-relawan\Volunteer');
        }
    }
}
