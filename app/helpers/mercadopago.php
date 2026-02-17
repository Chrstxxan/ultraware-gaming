<?php

require_once __DIR__.'/../config/env.php';

function criarPreferenciaMP($orderId, $total){

    $token = env('MP_ACCESS_TOKEN');

    $body = [
        "items" => [[
            "title" => "Pedido #$orderId - UltraWare Gaming",
            "quantity" => 1,
            "currency_id" => "BRL",
            "unit_price" => (float)$total
        ]],

        "external_reference" => (string)$orderId,

        "notification_url" => "https://SEU_SITE/webhook.php",

        "back_urls" => [
            "success" => "http://localhost/ultraware_gaming/public/pagamento_sucesso.php",
            "failure" => "http://localhost/ultraware_gaming/public/pagamento_falhou.php",
            "pending" => "http://localhost/ultraware_gaming/public/pagamento_pendente.php"
        ]
    ];

    $ch = curl_init("https://api.mercadopago.com/checkout/preferences");

    curl_setopt_array($ch,[
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_POST=>true,
        CURLOPT_HTTPHEADER=>[
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS=>json_encode($body)
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res,true);
}
