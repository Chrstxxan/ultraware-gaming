<?php

function cartCountDB($pdo, $userId){

    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(quantity),0)
        FROM cart_items
        WHERE user_id = ?
    ");

    $stmt->execute([$userId]);

    return (int)$stmt->fetchColumn();
}



function addToCartDB($pdo, $userId, $variantId){

    // já existe?
    $stmt = $pdo->prepare("
        SELECT quantity 
        FROM cart_items 
        WHERE user_id=? AND variant_id=?
    ");
    $stmt->execute([$userId,$variantId]);

    if($stmt->fetch()){
        $stmt = $pdo->prepare("
            UPDATE cart_items
            SET quantity = quantity + 1
            WHERE user_id=? AND variant_id=?
        ");
        $stmt->execute([$userId,$variantId]);
    }
    else{
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (user_id, variant_id, quantity)
            VALUES (?,?,1)
        ");
        $stmt->execute([$userId,$variantId]);
    }
}



function updateCartDB($pdo,$userId,$variantId,$delta){

    $stmt = $pdo->prepare("
        UPDATE cart_items
        SET quantity = quantity + ?
        WHERE user_id=? AND variant_id=?
    ");
    $stmt->execute([$delta,$userId,$variantId]);

    // remove se zerar
    $stmt = $pdo->prepare("
        DELETE FROM cart_items
        WHERE user_id=? AND variant_id=? AND quantity <= 0
    ");
    $stmt->execute([$userId,$variantId]);
}



function removeFromCartDB($pdo,$userId,$variantId){

    $stmt = $pdo->prepare("
        DELETE FROM cart_items
        WHERE user_id=? AND variant_id=?
    ");
    $stmt->execute([$userId,$variantId]);
}
