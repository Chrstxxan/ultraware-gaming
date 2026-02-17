<?php

require_once __DIR__.'/app/config/database.php';
require_once __DIR__.'/app/config/env.php';

/* ================= VALIDAR ASSINATURA ================= */

$secret = env('MP_WEBHOOK_SECRET');

if($secret){

    $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';

    if(!$signature || !$requestId){
        http_response_code(401);
        exit;
    }
}

/* ================= RECEBER EVENTO ================= */

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

if(empty($data['data']['id'])){
    http_response_code(200);
    exit;
}

$paymentId = $data['data']['id'];

/* ================= CONSULTAR PAGAMENTO ================= */

$token = env('MP_ACCESS_TOKEN');

$ch = curl_init("https://api.mercadopago.com/v1/payments/$paymentId");

curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token"
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

$payment = json_decode($response,true);

if(empty($payment['external_reference'])){
    http_response_code(200);
    exit;
}

$orderId = $payment['external_reference'];
$status  = $payment['status'];

/* ================= MAPEAR STATUS ================= */

$map = [
    'approved' => 'paid',
    'pending' => 'pending',
    'in_process' => 'pending',
    'rejected' => 'failed',
    'cancelled' => 'failed',
    'refunded' => 'refunded'
];

$newStatus = $map[$status] ?? 'pending';

/* evita sobrescrever pago */
$stmt = $pdo->prepare("SELECT payment_status FROM orders WHERE id=?");
$stmt->execute([$orderId]);
$current = $stmt->fetchColumn();

if($current === 'paid'){
    http_response_code(200);
    exit;
}

/* ================= ATUALIZAR PEDIDO ================= */

$stmt = $pdo->prepare("
    UPDATE orders
    SET payment_status=?, payment_id=?
    WHERE id=?
");

$stmt->execute([$newStatus,$paymentId,$orderId]);

http_response_code(200);
