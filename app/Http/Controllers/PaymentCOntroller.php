<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentCOntroller extends Controller
{
    //

    public function credit() {
        $token = $this->getToken();
        $order = $this->createOrder($token);
        $paymentToken = $this->getPaymentToken($order, $token);
        // return
        return ('https://accept.paymob.com/api/acceptance/iframes/'.env('PAYMOB_IFRAME_ID').'?payment_token='.$paymentToken);
    }

    /**
     * step 1
     *
     * Auth Token
     */
    public function getToken() {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens',[
            'api_key'=> env("PAYMENT_APY_KEY")
        ]);
        return $response->object()->token;
    }
    /**
     * step 2
     *
     * create Order
     */

    public function createOrder($token) {
        // $token = $this->getToken();//for test route
        $items = [
            [ "name"=> "ASC1515",
                "amount_cents"=> "500000",
                "description"=> "Smart Watch",
                "quantity"=> "1"
            ],
            [
                "name"=> "ERT6565",
                "amount_cents"=> "200000",
                "description"=> "Power Bank",
                "quantity"=> "1"
            ]
        ];

        $data = [
            "auth_token" => $token,
            "delivery_needed" =>"false",
            "amount_cents"=> "100",
            "currency"=> "EGP",
            "items"=> $items,

        ];
        $response = Http::post('https://accept.paymob.com/api/ecommerce/orders', $data);
        return $response->object();
    }

    /**
     * step 3
     * paymentKey
     */
    public function getPaymentToken($order, $token)
    {
        // $order = $this->createOrder()->id;
        // $token = $this->getToken();
        $billingData = [
            "apartment" => "803",
            "email" => "claudette09@exa.com",
            "floor" => "42",
            "first_name" => "Clifford",
            "street" => "Ethan Land",
            "building" => "8028",
            "phone_number" => "+86(8)9135210487",
            "shipping_method" => "PKG",
            "postal_code" => "01898",
            "city" => "Jaskolskiburgh",
            "country" => "CR",
            "last_name" => "Nicolas",
            "state" => "Utah"
        ];
        $data = [
            "auth_token" => $token,
            "amount_cents" => "100",
            "expiration" => 3600,
            "order_id" => $order->id,
            "billing_data" => $billingData,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_INTEGRATION_ID')
        ];
        $response = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', $data);
        return $response->object()->token;
    }

    /**
     * CallBack
     */
    public function callback(Request $request)
    {
        return "finish";
    }
}
