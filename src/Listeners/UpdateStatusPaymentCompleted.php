<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\PaymentCompleted;

class UpdateStatusPaymentCompleted
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
        $event->donation->update([
            'status' => 3,
            'manual_transaction' => 0
        ]);
    }
}
