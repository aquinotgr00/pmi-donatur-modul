<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\Campaign;

class MidtransController extends Controller
{   
    private $donation;

    function __construct(Donation $donation) {

        $this->donation = $donation;

    }

    public function charge(Request $request) {
        $api_url    = config('donation.payment_gateway.is_production') ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $server_key = config('donation.payment_gateway.server_key');

        $client     = new Client();
        $response   = $client->post(
            $api_url,
            [
            'headers'=>[
            'Content-Type'=>'application/json',
            'Accept'=>'application/json',
            'Authorization'=>'Basic ' . base64_encode($server_key . ':')
            ],
            'json'=>json_decode($request->getContent())
            ]);
        return $response->getBody();
    }

    public function notification(Request $request) {

        $result     = $request->json()->all();
        
        $donation   = $this->donation->find($result['order_id']);
        
        if (!is_null($donation)) {
            switch ($result['transaction_status']) {
                case 'settlement':

                $donation->update([
                    'status' => 3,
                    'manual_transaction' => 0
                ]);
                $campaign       = Campaign::find($donation->campaign_id);

                // increment amount_real campaign is fundraising true
                if (!is_null($campaign) && ($campaign->fundraising)) { 
                    
                    $amount_real    = intval($result['gross_amount']);
                    $amount_real    += intval($campaign->amount_real); 
                    $campaign->amount_real = $amount_real;
                    $campaign->save();
                    
                }
                break;

                case 'expire':

                $donation->update([
                    'status' => 4,
                    'manual_transaction' => 0
                ]);                    

                break;
            }
        }
        
        return response('', 200);
    }

    public function finish(Request $request){    }

    public function unfinish(Request $request)
    {
        return response()->json($request->json()->all());
    }

    public function error(Request $request)
    {
        return response()->json($request->json()->all());
    }
}
