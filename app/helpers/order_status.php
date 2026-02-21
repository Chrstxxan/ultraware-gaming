<?php

function getPublicOrderStatus(PDO $pdo, int $orderId): string {

    // pega último status logístico
    $stmt = $pdo->prepare("
        SELECT status
        FROM order_status_history
        WHERE order_id=?
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $status = $stmt->fetchColumn();

    if(!$status){
        return 'pending';
    }

    return $status;
}