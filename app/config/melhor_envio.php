<?php

require_once __DIR__ . "/../helpers/http.php";
require_once __DIR__.'/env.php';

function melhorEnvioCalcular($cepDestino){

    $token = env('MELHOR_ENVIO_TOKEN');

    $url = "https://www.melhorenvio.com.br/api/v2/me/shipment/calculate";

    $payload = [
        "from" => [
            "postal_code" => env('STORE_POSTAL_CODE')
        ],

        "to" => [
            "postal_code" => preg_replace('/[^0-9]/','',$cepDestino)
        ],

        "products" => [
            [
                "id" => "1",
                "width" => 20,
                "height" => 5,
                "length" => 30,
                "weight" => 0.8,
                "insurance_value" => 150,
                "quantity" => 1
            ]
        ]
    ];

    return httpPost($url, $token, $payload);
}
