<?php

require_once __DIR__ . "/../config/melhor_envio.php";

function calculateShipping($cep){

    $response = melhorEnvioCalcular($cep);

    /* ================= VALIDAÇÃO DA RESPOSTA ================= */

    if(!$response || !isset($response['ok'])){
        return ["erro" => "Falha na comunicação com API"];
    }

    if(!$response['ok']){
        return [
            "erro" => "Erro HTTP na API",
            "status" => $response['http_status'] ?? null
        ];
    }

    $services = $response['data'];

    if(!$services || !is_array($services)){
        return ["erro" => "Resposta vazia do Melhor Envio"];
    }

    /* ================= FILTRA SOMENTE CORREIOS ================= */

    $correios = [];

    foreach($services as $service){

        // ignora serviços com erro
        if(isset($service['error']) && $service['error'] !== null){
            continue;
        }

        // somente correios
        if(!isset($service['company']['name']) || $service['company']['name'] !== "Correios"){
            continue;
        }

        // pega preço independente do campo
        $price =
            $service['custom_price'] ??
            $service['final_price'] ??
            $service['price'] ??
            null;

        if(!$price) continue;

        $correios[] = [
            "nome"  => $service['name'],
            "valor" => (float)$price,
            "prazo" => (int)($service['delivery_time'] ?? 0)
        ];
    }

    if(empty($correios)){
        return ["erro" => "Correios indisponível para este CEP"];
    }

    /* ================= ESCOLHE O MAIS BARATO ================= */

    usort($correios, function($a,$b){
        return $a['valor'] <=> $b['valor'];
    });

    return $correios[0];
}
