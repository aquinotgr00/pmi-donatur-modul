<?php

namespace BajakLautMalaka\PmiDonatur\Events;

use BajakLautMalaka\PmiDonatur\Donation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DonationSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $donation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }
}
