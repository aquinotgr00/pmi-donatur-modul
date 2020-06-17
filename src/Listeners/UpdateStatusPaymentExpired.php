<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\PaymentExpired;

class UpdateStatusPaymentExpired
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
        $event->donation->update([
            'status' => 4,
            'manual_transaction' => 0
        ]);
    }
}
