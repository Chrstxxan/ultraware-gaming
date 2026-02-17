<?php

function httpPost($url, $token = null, $payload = null)
{
    $ch = curl_init($url);

    $headers = [
        "Content-Type: application/json",
        "Accept: application/json",
        "User-Agent: UltraWareGaming/1.0"
    ];

    if ($token) {
        $headers[] = "Authorization: Bearer " . $token;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);

    curl_close($ch);

    // erro de transporte (DNS, SSL, conexão etc)
    if ($error) {
        return [
            "ok" => false,
            "type" => "curl_error",
            "message" => $error
        ];
    }

    $data = json_decode($response, true);

    // resposta não é JSON
    if ($data === null) {
        return [
            "ok" => false,
            "type" => "invalid_json",
            "http_status" => $status,
            "raw_response" => $response
        ];
    }

    // erro HTTP vindo da API
    if ($status >= 400) {
        return [
            "ok" => false,
            "type" => "http_error",
            "http_status" => $status,
            "api_response" => $data
        ];
    }

    // sucesso
    return [
        "ok" => true,
        "http_status" => $status,
        "data" => $data
    ];
}
