<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\PaymentExpired;
use BajakLautMalaka\PmiDonatur\Mail\DonationRejectedMail;

class SendPaymentExpiredMail
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
     * @param  PaymentExpired  $event
     * @return void
     */
    public function handle(PaymentExpired $event)
    {
        \Mail::to($event->donation->email)->send(
            new DonationRejectedMail($event->donation)
        );
    }
}
