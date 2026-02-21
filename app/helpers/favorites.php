<?php

function isFavorite($pdo,$userId,$productId){
    $stmt=$pdo->prepare("
        SELECT 1 FROM favorites
        WHERE user_id=? AND product_id=? LIMIT 1
    ");
    $stmt->execute([$userId,$productId]);
    return (bool)$stmt->fetchColumn();
}

function favoritesCount($pdo,$productId){
    $stmt=$pdo->prepare("
        SELECT COUNT(*) FROM favorites WHERE product_id=?
    ");
    $stmt->execute([$productId]);
    return (int)$stmt->fetchColumn();
}

function userFavoritesIds($pdo,$userId){
    $stmt=$pdo->prepare("SELECT product_id FROM favorites WHERE user_id=?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}