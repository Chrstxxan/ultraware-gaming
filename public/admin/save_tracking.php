<?php
require_once "../../app/config/path.php";
require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

if (!isLogged() || !user()['admin']) requireLogin();

$orderId = (int)$_POST['order_id'];
$code = trim($_POST['tracking_code']);
$carrier = trim($_POST['carrier']);

if(!$orderId || !$code) die("Dados inválidos");

/* UPSERT */
$stmt = $pdo->prepare("
INSERT INTO order_tracking(order_id,carrier,tracking_code)
VALUES(?,?,?)
ON DUPLICATE KEY UPDATE
carrier=VALUES(carrier),
tracking_code=VALUES(tracking_code)
");

$stmt->execute([$orderId,$carrier,$code]);

header("Location: order.php?id=".$orderId);
exit;