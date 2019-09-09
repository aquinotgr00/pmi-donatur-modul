<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\PaymentCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateAmountRealDonation
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
        $campaign        = $event->donation->campaign()->first();
        if (!is_null($campaign)) {
            $amount_real     = intval($event->donation->amount);
            $amount_real    += intval($campaign->amount_real);
            $campaign->amount_real = $amount_real;
            $campaign->save();
        } 
    }
}
