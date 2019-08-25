<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use BajakLautMalaka\PmiDonatur\Events\CampaignPublished;
use BajakLautMalaka\PmiDonatur\Listeners\SendPublishedCampaignNotification;

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
