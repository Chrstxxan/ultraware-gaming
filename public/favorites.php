<?php
require_once "../app/config/path.php";
require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

requireLogin();

$userId = $_SESSION['user']['id'];

/* pega produtos favoritos */
$stmt = $pdo->prepare("
SELECT p.*, 
       MIN(v.preco) preco,
       (SELECT path 
        FROM product_images pi 
        JOIN product_variants vv ON vv.id = pi.variant_id
        WHERE vv.product_id = p.id
        LIMIT 1) img
FROM favorites f
JOIN products p ON p.id = f.product_id
JOIN product_variants v ON v.product_id = p.id
WHERE f.user_id = ?
GROUP BY p.id
ORDER BY f.created_at DESC
");

$stmt->execute([$userId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT."/views/products/favorites.php";