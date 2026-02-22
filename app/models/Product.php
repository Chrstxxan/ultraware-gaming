<?php

class Product {

    public static function all($pdo, $categoryId = null) {

        $sql = "
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
        ";

        $params = [];

        if($categoryId){
            $sql .= " WHERE p.category_id = ? ";
            $params[] = $categoryId;
        }

        $sql .= "
            GROUP BY p.id
            ORDER BY p.id DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function recommended(PDO $pdo, int $productId, int $limit = 8){

        /* ================= 1) DESCOBRE IDS POR COMPORTAMENTO ================= */

        $sql = "
            SELECT f2.product_id, COUNT(*) score
            FROM favorites f1
            JOIN favorites f2 ON f1.user_id = f2.user_id
            WHERE f1.product_id = ?
            AND f2.product_id != ?
            GROUP BY f2.product_id
            ORDER BY score DESC
            LIMIT $limit
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId,$productId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);


        /* ================= 2) COMPLEMENTA COM MESMA CATEGORIA ================= */

        if(count($ids) < $limit){

            $stmt = $pdo->prepare("
                SELECT id
                FROM products
                WHERE category_id = (
                    SELECT category_id FROM products WHERE id = ?
                )
                AND id != ?
                ORDER BY RAND()
                LIMIT $limit
            ");

            $stmt->execute([$productId,$productId]);

            $ids = array_unique(
                array_merge($ids, $stmt->fetchAll(PDO::FETCH_COLUMN))
            );
        }


        /* ================= 3) FALLBACK FINAL ================= */

        if(count($ids) < $limit){

            $stmt = $pdo->prepare("
                SELECT id
                FROM products
                WHERE id != ?
                ORDER BY RAND()
                LIMIT $limit
            ");

            $stmt->execute([$productId]);

            $ids = array_unique(
                array_merge($ids, $stmt->fetchAll(PDO::FETCH_COLUMN))
            );
        }


        /* Se ainda assim não houver nada (ex: só 1 produto no sistema) */
        if(empty($ids))
            return [];


        /* ================= 4) MONTA PRODUTOS COMPLETOS ================= */

        $in = implode(',', array_fill(0,count($ids),'?'));

        $sql = "
            SELECT 
                p.id,
                p.nome,
                p.descricao,
                MIN(v.preco) as preco,
                (
                    SELECT pi.path
                    FROM product_images pi
                    JOIN product_variants vv ON vv.id = pi.variant_id
                    WHERE vv.product_id = p.id
                    LIMIT 1
                ) as img
            FROM products p
            JOIN product_variants v ON v.product_id = p.id
            WHERE p.id IN ($in)
            GROUP BY p.id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
