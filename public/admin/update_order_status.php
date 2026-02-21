<?php
require_once "../../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";
require_once ROOT . "/app/helpers/auth.php";

if (!isLogged() || !user()['admin']) {
    requireLogin();
}

$orderId = (int)($_POST['order_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';

if(!$orderId || !$newStatus){
    die("Dados inválidos");
}

/* pega status atual */
$stmt = $pdo->prepare("
SELECT status
FROM order_status_history
WHERE order_id=?
ORDER BY created_at DESC
LIMIT 1
");
$stmt->execute([$orderId]);
$current = $stmt->fetchColumn() ?: 'novo';

/* fluxo permitido */
$allowed = [
    'novo' => ['pago','cancelado'],
    'pago' => ['preparando','cancelado'],
    'preparando' => ['enviado'],
    'enviado' => ['entregue'],
    'entregue' => [],
    'cancelado' => []
];

if(!in_array($newStatus, $allowed[$current])){
    die("Transição inválida");
}

/* não permitir enviado sem rastreio */
if($newStatus === 'enviado'){

    $stmt=$pdo->prepare("SELECT id FROM order_tracking WHERE order_id=?");
    $stmt->execute([$orderId]);

    if(!$stmt->fetch()){
        die("Cadastre o código de rastreio antes de marcar como enviado.");
    }
}

/* registra histórico */
$stmt = $pdo->prepare("
INSERT INTO order_status_history(order_id,status,created_at)
VALUES(?,?,NOW())
");
$stmt->execute([$orderId,$newStatus]);

/* atualiza cache */
$pdo->prepare("UPDATE orders SET situacao=? WHERE id=?")
    ->execute([$newStatus,$orderId]);

header("Location: order.php?id=".$orderId);
exit;