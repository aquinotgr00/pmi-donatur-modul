<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use BajakLautMalaka\PmiDonatur\Events\CampaignPublished;
use BajakLautMalaka\PmiDonatur\Events\PaymentCompleted;
use BajakLautMalaka\PmiDonatur\Events\PaymentExpired;
use BajakLautMalaka\PmiDonatur\Events\DonationSubmitted;
use BajakLautMalaka\PmiDonatur\Listeners\SendPublishedCampaignNotification;
use BajakLautMalaka\PmiDonatur\Listeners\SendPaymentCompletedMail;
use BajakLautMalaka\PmiDonatur\Listeners\UpdateAmountRealDonation;
use BajakLautMalaka\PmiDonatur\Listeners\UpdateStatusPaymentCompleted;
use BajakLautMalaka\PmiDonatur\Listeners\SendPaymentExpiredMail;
use BajakLautMalaka\PmiDonatur\Listeners\UpdateStatusPaymentExpired;
use BajakLautMalaka\PmiDonatur\Listeners\SendDonationSubmittedMail;

class PmiDonaturEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CampaignPublished::class => [
            SendPublishedCampaignNotification::class,
        ],
        PaymentCompleted::class => [
            SendPaymentCompletedMail::class,
            UpdateAmountRealDonation::class,
            UpdateStatusPaymentCompleted::class,
        ],
        PaymentExpired::class => [
            SendPaymentExpiredMail::class,
            UpdateStatusPaymentExpired::class,
        ],
        DonationSubmitted::class => [
            SendDonationSubmittedMail::class,
        ],
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    // TODO : investigate why this doesn't work! (Paul)

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
