<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\DonationSubmitted;
use BajakLautMalaka\PmiDonatur\Mail\DonationPendingMail;

class SendDonationSubmittedMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DonationSubmitted  $event
     * @return void
     */
    public function handle(DonationSubmitted $event)
    {
        \Mail::to($event->donation->email)->send(
            new DonationPendingMail($event->donation)
        );
    }
}
