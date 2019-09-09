<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\Campaign;
use BajakLautMalaka\PmiDonatur\Events\PaymentCompleted;
use BajakLautMalaka\PmiDonatur\Events\PaymentExpired;

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
        
        if (isset($result['transaction_status'])) {
            switch ($result['transaction_status']) {
                case 'settlement':
                event(new PaymentCompleted($donation));
                break;
                case 'expire':
                event(new PaymentExpired($donation));
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
