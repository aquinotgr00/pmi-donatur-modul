<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\CampaignPublished;
use Berkayk\OneSignal\OneSignalClient;

class SendPublishedCampaignNotification
{
    private $pushNotificationClient;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OneSignalClient $pushNotificationClient)
    {
        $this->pushNotificationClient = $pushNotificationClient;
    }

    /**
     * Handle the event.
     *
     * @param  CampaignPublished  $event
     * @return void
     */
    public function handle(CampaignPublished $event)
    {
        $this->pushNotificationClient->sendNotificationToAll($event->campaign->title, null, null, null, null);
    }
}
