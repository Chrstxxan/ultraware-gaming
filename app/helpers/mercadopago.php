<?php

require_once __DIR__.'/../config/env.php';

function criarPreferenciaMP($orderId, $total){

    $token = env('MP_ACCESS_TOKEN');
    $base  = rtrim(env('APP_URL'),'/');

    $body = [
        "items" => [[
            "id" => (string)$orderId,
            "title" => "Pedido #$orderId - UltraWare Gaming",
            "quantity" => 1,
            "currency_id" => "BRL",
            "unit_price" => (float)$total
        ]],

        "external_reference" => (string)$orderId,

        "notification_url" => $base."/webhook.php",

        "back_urls" => [
            "success" => $base."/public/pagamento_sucesso.php",
            "failure" => $base."/public/pagamento_falhou.php",
            "pending" => $base."/public/pagamento_pendente.php"
        ],

        "auto_return" => "approved"
    ];

    $ch = curl_init("https://api.mercadopago.com/checkout/preferences");

    curl_setopt_array($ch,[
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($body)
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res,true);
}
