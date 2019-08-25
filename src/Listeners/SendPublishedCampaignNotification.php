<?php

namespace BajakLautMalaka\PmiDonatur\Listeners;

use BajakLautMalaka\PmiDonatur\Events\CampaignPublished;
use Berkayk\OneSignal\OneSignalClient;

class SendPublishedCampaignNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  CampaignPublished  $event
     * @return void
     */
    public function handle(CampaignPublished $event)
    {
        $pushNotificationAppId = config('donation.push_notification.app_id',env('ONESIGNAL_APP_ID'));
        $pushNotificationRestApiKey = config('donation.push_notification.rest_api_key',env('ONESIGNAL_REST_API_KEY'));
        $pushNotificationClient = new OneSignalClient($pushNotificationAppId, $pushNotificationRestApiKey, $pushNotificationRestApiKey);
        
        $pushNotificationClient->sendNotificationToAll($event->campaign->title, null, null, null, null);
    }
}
