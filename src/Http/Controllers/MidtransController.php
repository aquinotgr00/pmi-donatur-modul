<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


class MidtransController extends Controller
{
    public function charge(Request $request) {
    
        $api_url = config('midtrans.is_production') ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $server_key = config('midtrans.server_key');

        $client = new Client();
        $response = $client->post(
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
}
