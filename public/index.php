<?php
require_once "../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";
require_once ROOT . "/app/models/Product.php";

$category = $_GET['categoria'] ?? null;

/* ================= MAIS DESEJADOS ================= */

$stmt = $pdo->query("
SELECT p.id, p.nome,
       MIN(v.preco) AS preco,
       COUNT(f.product_id) AS total_favs,
       (
         SELECT pi.path
         FROM product_images pi
         JOIN product_variants vv ON vv.id = pi.variant_id
         WHERE vv.product_id = p.id
         LIMIT 1
       ) AS img
FROM favorites f
JOIN products p ON p.id = f.product_id
JOIN product_variants v ON v.product_id = p.id
GROUP BY p.id
ORDER BY total_favs DESC
LIMIT 6
");

$mostWanted = $stmt->fetchAll(PDO::FETCH_ASSOC);

$products = Product::all($pdo, $category);

require_once ROOT . "/views/products/list.php";
