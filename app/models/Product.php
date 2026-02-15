<?php

class Product {

    public static function all($pdo) {

        $stmt = $pdo->query("
            SELECT 
                p.id,
                p.nome,
                p.descricao,
                MIN(v.preco) as preco,
                (
                    SELECT path 
                    FROM product_images pi
                    WHERE pi.variant_id = v.id
                    LIMIT 1
                ) as img
            FROM products p
            JOIN product_variants v ON v.product_id = p.id
            GROUP BY p.id
            ORDER BY p.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
