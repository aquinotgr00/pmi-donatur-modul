<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\PaymentCompleted;
use BajakLautMalaka\PmiDonatur\Mail\DonationCompletedMail;

class SendPaymentCompletedMail
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
     * @param  PaymentCompleted  $event
     * @return void
     */
    public function handle(PaymentCompleted $event)
    {
        \Mail::to($event->donation->email)->send(
            new DonationCompletedMail($event->donation)
        );
    }
}
