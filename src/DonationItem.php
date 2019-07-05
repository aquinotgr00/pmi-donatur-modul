<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Database\Eloquent\Model;

class DonationItem extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'donation_id', 'type', 'name', 'amount'
    ];

    public function donation()
    {
        if (class_exists('Donation')) {
            return $this->belongsTo('Donation');
        }
    }
}
